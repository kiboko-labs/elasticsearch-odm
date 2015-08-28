<?php

namespace Mosiyash\ElasticSearch\Tests;

use Mosiyash\ElasticSearch\DocumentRepositoryAbstract;

class CustomDocumentRepository extends DocumentRepositoryAbstract
{
    public function getIndex()
    {
        return 'tests';
    }

    public function getType()
    {
        return 'custom';
    }
}
