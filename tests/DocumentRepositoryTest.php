<?php

namespace Mosiyash\Elasticsearch;

use Mosiyash\Elasticsearch\QueryParams\Search;
use Mosiyash\Elasticsearch\Tests\CustomDocumentRepository;
use React\EventLoop\StreamSelectLoop;
use React\Promise\Promise;
use React\Promise\Timer;
use React\Promise\Deferred;

class DocumentRepositoryTest extends TestCase
{
    /**
     * @return CustomDocument
     */
    protected function newCustomDocument()
    {
        $document = $this->di->get('tests/documents:custom')->__invoke();
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
        return $this->di->get('tests/documents:custom_repository');
    }

    public function testFind()
    {
        $document = $this->newCustomDocument();
        $repository = $this->getCustomDocumentRepository();

        $result = $repository->find('xyz');
        $this->assertInstanceOf('Mosiyash\Elasticsearch\Tests\CustomDocument', $result);
        $this->assertFalse($result->isNew());
        $this->assertEquals($document, $result);

        $result = $repository->find('undefined');
        $this->assertNull($result);
    }

    public function testFindBy()
    {
        $document = $this->newCustomDocument();
        $document->version = null;

        $expected = [$document];
        $actual = function() {
            $repository = $this->getCustomDocumentRepository();
            $params = new Search($repository);
            $params->body = ['query' => ['match' => ['firstname' => 'John']]];
            $result = $repository->findBy($params);
            return $result;
        };
        $resolver = function($expected, $actual) {
            $this->assertEquals($expected, $actual);
        };
        $this->assertPromise($expected, $actual, $resolver);
    }
}
