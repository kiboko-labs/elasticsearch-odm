<?php

namespace Mosiyash\ElasticSearch;

use Mosiyash\ElasticSearch\Tests\CustomDocumentRepository;

class DocumentRepositoryTest extends TestCase
{
    /**
     * @return CustomDocument
     */
    protected function newCustomDocument()
    {
        $document = $this->di->get('tests/documents:custom');
        $document->id = 'xyz';
        $document->firstname = 'John';
        $document->lastname = 'Doe';
        $result = $document->save();
        $this->assertSame('xyz', $result['_id']);

        return $document;
    }

    /**
     * @return CustomDocumentRepository
     */
    public function getCustomDocumentRepository()
    {
        return $this->di->get('tests/repositories:custom');
    }

    public function testFindById()
    {
        $document = $this->newCustomDocument();
        $repository = $this->getCustomDocumentRepository();

        $founded = $repository->findOneById('xyz');
        $this->assertInstanceOf('Mosiyash\ElasticSearch\Tests\CustomDocument', $founded);
        $this->assertFalse($founded->isNew());
        $this->assertEquals($document, $founded);
    }
}