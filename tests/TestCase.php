<?php

namespace Mosiyash\Elasticsearch;

use Aura\Di\Container;
use Aura\Di\Factory;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Symfony\Component\Process\Process;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    protected $di;

    public function setUp()
    {
        $this->di = new Container(new Factory());

        $this->di->set('tests/elasticsearch:client', $this->di->lazy(function() {
            return ClientBuilder::create()->build();
        }));

        $this->di->setter['Mosiyash\Elasticsearch\DocumentAbstract']['setDi'] = $this->di;
        $this->di->setter['Mosiyash\Elasticsearch\DocumentRepositoryAbstract']['setDi'] = $this->di;

        $this->di->set('tests/documents:custom', $this->di->newFactory('Mosiyash\Elasticsearch\Tests\CustomDocument'));
        $this->di->set('tests/documents:custom_repository', $this->di->lazyNew('Mosiyash\Elasticsearch\Tests\CustomDocumentRepository'));

        $this->checkElasticsearchIsRunned();
        $this->deleteElasticsearchIndex();
        $this->createElasticsearchIndex();
    }

    public function tearDown()
    {
        // $this->deleteElasticsearchIndex();
    }

    protected function checkElasticsearchIsRunned()
    {
        $osname = strtolower(php_uname('s'));

        if ($osname === 'windows') {
            $cmd = 'tasklist | find "elasticsearch"';
        } else {
            $cmd = 'ps aux | grep elasticsearch';
        }

        $process = new Process($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $output = $process->getOutput();
        $this->assertRegExp('/java.+elasticsearch/i', $output);
    }

    protected function deleteElasticsearchIndex()
    {
        $params = ['index' => 'tests'];

        try {
            $response = $this->di->get('tests/elasticsearch:client')->indices()->delete($params);
            $this->assertTrue($response['acknowledged']);
        } catch (Missing404Exception $e) {
            // Index already deleted.
        }
    }

    protected function createElasticsearchIndex()
    {
        $params = [
            'index' => 'tests',
            'body' => [
                'mappings' => [
                    'custom' => [
                        'enabled' => true,
                        'properties' => [
                            'firstname' => [
                                'type' => 'string',
                                'index' => 'not_analyzed',
                            ],
                            'lastname' => [
                                'type' => 'string',
                                'index' => 'not_analyzed',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->di->get('tests/elasticsearch:client')->indices()->create($params);
        $this->assertTrue($response['acknowledged']);
    }

    public function assertPromise($expected, callable $actual, callable $resolver, $timeout = 5)
    {
        $start = microtime(true);
        $runned = true;

        while ($runned) {
            $actualValue = call_user_func($actual);
            $fail = false;

            try {
                call_user_func_array($resolver, [$expected, $actualValue]);
                $runned = false;
            } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
                $passed = microtime(true) - $start;

                if ($passed >= $timeout) {
                    $fail = true;
                }
            }

            if ($fail) {
                throw new \PHPUnit_Framework_ExpectationFailedException($e->getMessage(), $e->getComparisonFailure(), $e->getPrevious());
            }
        }
    }
}
