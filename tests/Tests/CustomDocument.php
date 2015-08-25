<?php

namespace Mosiyash\ElasticSearch\Tests;

use Mosiyash\ElasticSearch\DocumentAbstract;

class CustomDocument extends DocumentAbstract
{
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

    public function getIndex()
    {
        return 'tests';
    }

    public function getType()
    {
        return 'custom';
    }
}