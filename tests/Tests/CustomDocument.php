<?php

namespace Mosiyash\Elasticsearch\Tests;

use Mosiyash\Elasticsearch\DocumentAbstract;
use Mosiyash\Elasticsearch\Mapping;

class CustomDocument extends DocumentAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getMapping()
    {
        return [
            'properties' => [
                'firstname' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
                'lastname' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            ],
        ];
    }

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
