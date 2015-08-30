<?php

namespace Mosiyash\ElasticSearch\QueryParams;
use Mosiyash\ElasticSearch\QueryParamsAbstract;

/**
 * Class Update
 *
 * TODO: add consistency property (https://www.elastic.co/search?q=consistency)
 * TODO: add perlocator property (https://www.elastic.co/guide/en/elasticsearch/reference/current/search-percolate.html)
 * TODO: add replication property (specific replication type)
 * TODO: add versionType property (specific version type)
 * TODO: add retryOnConflict property (specify how many times should the operation be retried when a conflict occurs)
 *
 * @package Mosiyash\ElasticSearch\QueryParams
 */
class Update extends QueryParamsAbstract
{
    /**
     * Specific document ID (when the POST method is used)
     *
     * @var string
     * @isQueryParameter
     */
    public $id;

    /**
     * A comma-separated list of fields to return in the response
     *
     * @var string
     * @isQueryParameter
     */
    public $fields;

    /**
     * The script language (default: mvel)
     *
     * @var string
     * @isQueryParameter
     */
    public $lang = 'mvel';

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
     * The URL-encoded script definition (instead of using request body)
     *
     * @var string
     */
    public $script;

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
     * The request definition using either `script` or partial `doc`
     *
     * @var array
     * @isQueryParameter
     */
    public $body = [];
}
