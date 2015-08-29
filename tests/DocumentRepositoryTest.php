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

    public function testFind()
    {
        $document = $this->newCustomDocument();
        $repository = $this->getCustomDocumentRepository();

        $result = $repository->find('xyz');
        $this->assertInstanceOf('Mosiyash\ElasticSearch\Tests\CustomDocument', $result);
        $this->assertFalse($result->isNew());
        $this->assertEquals($document, $result);

        $result = $repository->find('undefined');
        $this->assertNull($result);
    }

    public function testFindBy()
    {
        $documents = [$this->newCustomDocument()];
        $repository = $this->getCustomDocumentRepository();

        $result = $repository->findBy(['query' => ['match' => ['lastname' => 'Doe']]]);
        exit(print_r($result));
        //$this->assertEquals($documents, $result);
    }
}