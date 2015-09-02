<?php

namespace Mosiyash\Elasticsearch\Tests;

use Mosiyash\Elasticsearch\DocumentAbstract;

class CustomDocument extends DocumentAbstract
{
    /**
     * @return CustomDocumentRepository
     */
    public function getRepository()
    {
        return $this->di->get('tests/documents:custom_repository');
    }

    /**
     * @var string
     * @isBodyParameter
     */
    public $firstname;

    /**
     * @var string
     * @isBodyParameter
     */
    public $lastname;
}
