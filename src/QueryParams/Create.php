<?php

namespace Mosiyash\ElasticSearch\QueryParams;
use Mosiyash\ElasticSearch\QueryParamsAbstract;

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
class Create extends QueryParamsAbstract
{
    /**
     * Specific document ID (when the POST method is used)
     *
     * @var string
     * @isQueryParameter
     */
    public $id;

    /**
     * ID of the parent document
     *
     * @var string
     * @isQueryParameter
     */
    public $parent;

    /**
     * Refresh the index after performing the operation
     *
     * @var boolean
     * @isQueryParameter
     */
    public $refresh;

    /**
     * Specific routing value
     *
     * @var string
     * @isQueryParameter
     */
    public $routing;

    /**
     * Explicit operation timeout
     *
     * @var integer
     * @isQueryParameter
     */
    public $timeout;

    /**
     * Explicit timestamp for the document
     *
     * @var integer
     * @isQueryParameter
     */
    public $timestamp;

    /**
     * Expiration time for the document
     *
     * @var integer
     * @isQueryParameter
     */
    public $ttl;

    /**
     * Explicit version number for concurrency control
     *
     * @var integer
     * @isQueryParameter
     */
    public $version;

    /**
     * The document
     *
     * @var array
     * @isQueryParameter
     */
    public $body = [];
}
