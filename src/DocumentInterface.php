<?php

namespace Mosiyash\Elasticsearch;

interface DocumentInterface
{
    /**
     * @return DocumentRepositoryInterface
     */
    public function getRepository();
}
