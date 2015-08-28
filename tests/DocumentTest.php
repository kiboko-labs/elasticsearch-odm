<?php

namespace Mosiyash\ElasticSearch;

use Aura\Di\Container;
use Aura\Di\Factory;
use Mosiyash\ElasticSearch\Tests\CustomDocument;

class DocumentTest extends TestCase
{
    /**
     * @return CustomDocument
     */
    protected function newCustomDocument()
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
}
