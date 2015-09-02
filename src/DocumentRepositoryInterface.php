<?php

namespace Mosiyash\Elasticsearch;

use Elasticsearch\Client;

interface DocumentRepositoryInterface
{
    /**
     * @return Client
     */
    public function getClient();

    /**
     * @return string
     */
    public function getIndex();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return DocumentInterface
     */
    public function newDocument();
}
