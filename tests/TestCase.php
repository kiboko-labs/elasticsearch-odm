<?php

namespace Mosiyash\ElasticSearch;

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
        $this->di->set('tests/elasticsearch:client', function() { return ClientBuilder::create()->build(); });

        $this->di->set('tests/documents:custom', $this->di->lazyNew('Mosiyash\ElasticSearch\Tests\CustomDocument'));
        $this->di->setter['Mosiyash\ElasticSearch\Tests\CustomDocument']['setRepositoryServiceName'] = 'tests/repositories:custom';

        $this->di->set('tests/repositories:custom', $this->di->lazyNew('Mosiyash\ElasticSearch\Tests\CustomDocumentRepository'));

        $this->di->setter['Mosiyash\ElasticSearch\DocumentAbstract']['setDi'] = $this->di;
        $this->di->setter['Mosiyash\ElasticSearch\DocumentRepositoryAbstract']['setDi'] = $this->di;
        $this->di->setter['Mosiyash\ElasticSearch\DocumentRepositoryAbstract']['setClientServiceName'] = 'tests/elasticsearch:client';
        $this->di->setter['Mosiyash\ElasticSearch\DocumentRepositoryAbstract']['setDocumentClassName'] = 'Mosiyash\ElasticSearch\Tests\CustomDocument';

        $this->deleteElasticSearchIndex();
        $this->createElasticSearchIndex();
    }

    public function tearDown()
    {
        $this->deleteElasticSearchIndex();
    }

    public function deleteElasticSearchIndex()
    {
        $params = ['index' => 'tests'];

        try {
            $response = $this->di->get('tests/elasticsearch:client')->indices()->delete($params);
            $this->assertTrue($response['acknowledged']);
        } catch (Missing404Exception $e) {
            // Index already deleted.
        }
    }

    public function createElasticSearchIndex()
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
}
