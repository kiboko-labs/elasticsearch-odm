<?php

namespace Mosiyash\ElasticSearch;

use Aura\Di\Container;
use Aura\Di\Factory;
use Mosiyash\ElasticSearch\QueryParams\Create;
use Mosiyash\ElasticSearch\Tests\CustomDocument;

class DocumentTest extends \PHPUnit_Framework_TestCase
{
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
        $di->set('mosiyash/elasticsearch:tests:custom_document', $di->lazyNew('Mosiyash\ElasticSearch\Tests\CustomDocument'));
        $di->setter['Mosiyash\ElasticSearch\DocumentAbstract']['setDi'] = $di;

        $document = $this->newCustomDocument($di);
        $this->assertInstanceOf('Aura\Di\Container', $document->di);
        $this->assertSame('tests', $document->getIndex());
        $this->assertSame('custom', $document->getType());

        return $document;
    }

    /**
     * @param CustomDocument $document
     * @depends testNewCustomDocument
     */
    public function testCreate(CustomDocument $document)
    {
        // $params = new Create();
        // $document->queryParams = $params;
        // $this->assertInstanceOf('Mosiyash\ElasticSearch\QueryParams\Create', $document->queryParams);

        $this->assertSame(['firstname' => null, 'lastname' => null], $document->getBody());

        $document->firstname = 'John';
        $document->lastname = 'Doe';
        $this->assertSame(['firstname' => 'John', 'lastname' => 'Doe'], $document->getBody());
    }
}
