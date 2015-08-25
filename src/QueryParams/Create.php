<?php

namespace Mosiyash\ElasticSearch\QueryParams;

/**
 * Class Create
 *
 * TODO: add consistency property (https://www.elastic.co/search?q=consistency)
 * TODO: add perlocator property (https://www.elastic.co/guide/en/elasticsearch/reference/current/search-percolate.html)
 * TODO: add replication property (specific replication type)
 * TODO: add versionType property (specific version type)
 *
 * @package Mosiyash\ElasticSearch\QueryParams
 */
class Create
{
    /**
     * Specific document ID (when the POST method is used)
     *
     * @var string
     */
    public $id;

    /**
     * ID of the parent document
     *
     * @var string
     */
    public $parent;

    /**
     * Refresh the index after performing the operation
     *
     * @var boolean
     */
    public $refresh;

    /**
     * Specific routing value
     *
     * @var string
     */
    public $routing;

    /**
     * Explicit operation timeout
     *
     * @var integer
     */
    public $timeout;

    /**
     * Explicit timestamp for the document
     *
     * @var integer
     */
    public $timestamp;

    /**
     * Expiration time for the document
     *
     * @var integer
     */
    public $ttl;

    /**
     * Explicit version number for concurrency control
     *
     * @var integer
     */
    public $version;

    /**
     * The document
     *
     * @var array
     */
    public $body = [];
}
