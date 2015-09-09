<?php

namespace Mosiyash\Elasticsearch;

interface DocumentInterface
{
    /**
     * @return array
     */
    public function getMapping();

    /**
     * @return DocumentRepositoryInterface
     */
    public function getRepository();
}
