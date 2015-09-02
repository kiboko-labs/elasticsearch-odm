<?php

namespace Mosiyash\Elasticsearch;

use Aura\Di\Container;
use Aura\Di\Factory;
use Mosiyash\Elasticsearch\Tests\CustomDocument;

class DocumentTest extends TestCase
{
    /**
     * @return CustomDocument
     */
    protected function newCustomDocument()
    {
        $document = $this->di->get('tests/documents:custom')->__invoke();

        return $document;
    }

    public function testDocumentSetDi()
    {
        $document = new CustomDocument();
        $this->assertObjectHasAttribute('di', $document);
        $this->assertNull($document->di);

        $document->setDi(new Container(new Factory()));
        $this->assertInstanceOf('Aura\Di\Container', $document->di);

        $this->setExpectedException('Mosiyash\Elasticsearch\Exceptions\LogicException', 'The container is already bound');
        $document->setDi(new Container(new Factory()));
    }

    public function testNewCustomDocument()
    {
        $document = $this->newCustomDocument();
        $this->assertInstanceOf('Aura\Di\Container', $document->di);
        $this->assertSame('tests', $document->getIndex());
        $this->assertSame('custom', $document->getType());
        $this->assertTrue($document->isNew());
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
        $this->assertSame(['firstname' => 'John', 'lastname' => 'Doe'], $document->getBody());
        $this->assertTrue($document->isNew());

        $result = $document->save();
        $this->assertEquals($result, ['_index' => 'tests', '_type' => 'custom', '_id' => 1, '_version' => 1, 'created' => 1]);
        $this->assertFalse($document->isNew());
    }

    public function testCreateWithAutoId()
    {
        $document = $this->newCustomDocument();
        $this->assertTrue($document->isNew());

        $document->firstname = 'John';
        $result = $document->save();
        $this->assertEquals($result, ['_index' => 'tests', '_type' => 'custom', '_id' => $result['_id'], '_version' => 1, 'created' => 1]);
        $this->assertFalse($document->isNew());
        $this->assertSame(1, $document->version);
        $this->assertSame($result['_id'], $document->id);
        $this->assertSame(['firstname' => 'John', 'lastname' => null], $document->getBody());
    }

    public function testUpdate()
    {
        $document = $this->newCustomDocument();
        $document->firstname = 'John';
        $document->save();
        $this->assertSame(1, $document->version);

        $document->lastname = 'Doe';
        $document->save();
        $this->assertSame(2, $document->version);
        $this->assertEquals(['firstname' => 'John', 'lastname' => 'Doe'], $document->getBody());
    }

    public function testDelete()
    {
        $document = $this->newCustomDocument();
        $document->firstname = 'John';
        $document->save();
        $this->assertSame(1, $document->version);

        $document->delete();

        $this->setExpectedException('Elasticsearch\Common\Exceptions\Missing404Exception');
        $document->save();
    }
}
