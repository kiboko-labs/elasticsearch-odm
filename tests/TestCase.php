<?php

namespace Mosiyash\ElasticSearch;

use Aura\Di\Container;
use Aura\Di\Factory;
use Elasticsearch\ClientBuilder;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    protected $di;

    public function setUp()
    {
        exec('curl -XDELETE http://localhost:9200/tests 2>/dev/null');

        $this->di = new Container(new Factory());
        $this->di->set('tests/elasticsearch:client', function() { return ClientBuilder::create()->build(); });

        $this->di->set('tests/documents:custom', $this->di->lazyNew('Mosiyash\ElasticSearch\Tests\CustomDocument'));
        $this->di->setter['Mosiyash\ElasticSearch\Tests\CustomDocument']['setRepositoryServiceName'] = 'tests/repositories:custom';

        $this->di->set('tests/repositories:custom', $this->di->lazyNew('Mosiyash\ElasticSearch\Tests\CustomDocumentRepository'));

        $this->di->setter['Mosiyash\ElasticSearch\DocumentAbstract']['setDi'] = $this->di;
        $this->di->setter['Mosiyash\ElasticSearch\DocumentRepositoryAbstract']['setDi'] = $this->di;
        $this->di->setter['Mosiyash\ElasticSearch\DocumentRepositoryAbstract']['setClientServiceName'] = 'tests/elasticsearch:client';
        $this->di->setter['Mosiyash\ElasticSearch\DocumentRepositoryAbstract']['setDocumentClassName'] = 'Mosiyash\ElasticSearch\Tests\CustomDocument';
    }

    public function tearDown()
    {
        exec('curl -XDELETE http://localhost:9200/tests 2>/dev/null');
    }
}
