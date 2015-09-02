<?php

namespace Mosiyash\Elasticsearch\Tests;

use Mosiyash\Elasticsearch\DocumentRepositoryAbstract;

class CustomDocumentRepository extends DocumentRepositoryAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getClient()
    {
        return $this->di->get('tests/elasticsearch:client');
    }

    /**
     * {@inheritdoc}
     */
    public function getIndex()
    {
        return 'tests';
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'custom';
    }

    /**
     * @return CustomDocument
     */
    public function newDocument()
    {
        return $this->di->get('tests/documents:custom')->__invoke();
    }
}
