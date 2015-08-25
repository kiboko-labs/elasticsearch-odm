<?php

namespace Mosiyash\ElasticSearch;

interface DocumentInterface
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
