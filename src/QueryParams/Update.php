<?php

namespace Mosiyash\Elasticsearch\QueryParams;
use Mosiyash\Elasticsearch\QueryParamsDocumentAbstract;

/**
 * Class Update
 *
 * TODO: add consistency property (https://www.elastic.co/search?q=consistency)
 * TODO: add perlocator property (https://www.elastic.co/guide/en/elasticsearch/reference/current/search-percolate.html)
 * TODO: add replication property (specific replication type)
 * TODO: add versionType property (specific version type)
 * TODO: add retryOnConflict property (specify how many times should the operation be retried when a conflict occurs)
 *
 * @package Mosiyash\Elasticsearch\QueryParams
 */
class Update extends QueryParamsDocumentAbstract
{
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
     * The URL-encoded script definition (instead of using request body)
     *
     * @var string
     */
    public $script;

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
