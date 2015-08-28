<?php

namespace Mosiyash\ElasticSearch;

use Aura\Di\Container;
use Aura\Di\Factory;
use Elasticsearch\ClientBuilder;
use Mosiyash\ElasticSearch\QueryParams\Create;
use Mosiyash\ElasticSearch\Tests\CustomDocument;
use Mosiyash\ElasticSearch\Tests\CustomDocumentRepository;

class DocumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    private $di;

    public function setUp()
    {
        exec('curl -XDELETE http://localhost:9200/tests 2>/dev/null');

        $this->di = new Container(new Factory());
        $this->di->set('tests/elasticsearch:client', function() { return ClientBuilder::create()->build(); });

        $this->di->set('tests/documents:custom', $this->di->lazyNew('Mosiyash\ElasticSearch\Tests\CustomDocument'));
        $this->di->setter['Mosiyash\ElasticSearch\Tests\CustomDocument']['setRepositoryServiceName'] = 'tests/repositories:custom';

        $this->di->set('tests/repositories:custom', $this->di->lazyNew('Mosiyash\ElasticSearch\Tests\CustomDocumentRepository'));

        $this->di->setter['Mosiyash\ElasticSearch\DocumentAbstract']['setDi'] = $this->di;
        $this->di->setter['Mosiyash\ElasticSearch\DocumentAbstract']['setClientServiceName'] = 'tests/elasticsearch:client';
    }

    public function tearDown()
    {
        exec('curl -XDELETE http://localhost:9200/tests 2>/dev/null');
    }

    /**
     * @return CustomDocument
     */
    final protected function newCustomDocument()
    {
        $document = $this->di->get('tests/documents:custom');

        return $document;
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
        $document = $this->newCustomDocument();
        $this->assertInstanceOf('Aura\Di\Container', $document->di);
        $this->assertSame('tests', $document->getIndex());
        $this->assertSame('custom', $document->getType());
        $this->assertTrue($document->isNew());

        return $document;
    }

    public function testCreate()
    {
        $document = $this->newCustomDocument();

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

    /**
     * return CustomDocument
     */
    public function testCreateWithAutoId()
    {
        $document = $this->newCustomDocument();
        $this->assertTrue($document->isNew());

        $document->firstname = 'John';
        $result = $document->save();
        $this->assertEquals($result, ['_index' => 'tests', '_type' => 'custom', '_id' => $result['_id'], '_version' => 1, 'created' => 1]);
        $this->assertFalse($document->isNew());
        $this->assertSame(['firstname' => 'John', 'lastname' => null, 'id' => $result['_id']], $document->getBody());
        $this->assertSame($result['_id'], $document->id);

        return $document;
    }

    /**
     * @param CustomDocument $document
     * @depends testCreateWithAutoId
     */
    public function testGet(CustomDocument $document)
    {
        $repository = new CustomDocumentRepository();
    }
}
