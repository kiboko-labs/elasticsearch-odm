<?php

namespace Mosiyash\ElasticSearch;

use Aura\Di\Container;
use Aura\Di\Factory;
use Elasticsearch\ClientBuilder;
use Mosiyash\ElasticSearch\QueryParams\Create;
use Mosiyash\ElasticSearch\Tests\CustomDocument;

class DocumentTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        exec('curl -XDELETE http://localhost:9200/tests 2>/dev/null');
    }

    /**
     * @param Container $di
     * @return CustomDocument
     */
    final protected function newCustomDocument(Container $di)
    {
        return $di->get('mosiyash/elasticsearch:tests:custom_document');
    }

    public function testDocumentSetDi()
    {
        $document = new CustomDocument();
        $this->assertObjectHasAttribute('di', $document);
        $this->assertNull($document->di);

        $document->setDi(new Container(new Factory()));
        $this->assertInstanceOf('Aura\Di\Container', $document->di);

        $this->setExpectedException('Mosiyash\ElasticSearch\Exceptions\LogicException', 'The container is already bound');
        $document->setDi(new Container(new Factory()));
    }

    /**
     * @return CustomDocument
     */
    public function testNewCustomDocument()
    {
        $di = new Container(new Factory());
        $di->set('mosiyash/elasticsearch:tests:client', function() {
            return ClientBuilder::create()->build();
        });
        $di->set('mosiyash/elasticsearch:tests:custom_document', $di->lazyNew('Mosiyash\ElasticSearch\Tests\CustomDocument'));
        $di->setter['Mosiyash\ElasticSearch\DocumentAbstract']['setDi'] = $di;
        $di->setter['Mosiyash\ElasticSearch\DocumentAbstract']['setClientServiceName'] = 'mosiyash/elasticsearch:tests:client';

        $document = $this->newCustomDocument($di);
        $this->assertInstanceOf('Aura\Di\Container', $document->di);
        $this->assertSame('tests', $document->getIndex());
        $this->assertSame('custom', $document->getType());
        $this->assertTrue($document->isNew());

        return $document;
    }

    /**
     * @param CustomDocument $document
     * @depends testNewCustomDocument
     */
    public function testCreate(CustomDocument $document)
    {
        $this->assertSame(['firstname' => null, 'lastname' => null], $document->getBody());
        $this->assertTrue($document->isNew());

        $document->firstname = 'John';
        $document->lastname = 'Doe';
        $this->assertSame(['firstname' => 'John', 'lastname' => 'Doe'], $document->getBody());
        $this->assertTrue($document->isNew());

        $document->id = 1;
        $this->assertSame(['firstname' => 'John', 'lastname' => 'Doe', 'id' => 1], $document->getBody());
        $this->assertTrue($document->isNew());

        $result = $document->save();
        $this->assertEquals($result, ['_index' => 'tests', '_type' => 'custom', '_id' => 1, '_version' => 1, 'created' => 1]);
        $this->assertFalse($document->isNew());
    }
}
