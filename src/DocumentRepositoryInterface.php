<?php

namespace Mosiyash\ElasticSearch;

interface DocumentRepositoryInterface
{
    /**
     * @return string
     */
    public function getIndex();

    /**
     * @return string
     */
    public function getType();
}
