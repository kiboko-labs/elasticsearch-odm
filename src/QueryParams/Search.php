<?php

namespace Mosiyash\ElasticSearch\QueryParams;

use Mosiyash\ElasticSearch\DocumentRepositoryInterface;
use Mosiyash\ElasticSearch\QueryParamsTrait;

/**
 * Class QueryParamsSearchAbstract
 *
 * TODO: add ignoreIndices property
 * TODO: add indicesBoost property
 * TODO: add lenient property
 * TODO: add lowercaseExpandedTerms property
 * TODO: add preference property
 * TODO: add q property
 * TODO: add routing property
 * TODO: add scroll property
 * TODO: add source property
 * TODO: add _source property
 * TODO: add _sourceExclude property
 * TODO: add _sourceInclude property
 * TODO: add stats property
 * TODO: add suggestField property
 * TODO: add suggestMode property
 * TODO: add suggestSize property
 * TODO: add suggestText property
 *
 * @package Mosiyash\ElasticSearch
 */
class Search
{
    use QueryParamsTrait;

    /**
     * @var DocumentRepositoryInterface
     */
    protected $repository;

    /**
     * @var string
     * @isQueryParameter
     */
    protected $index;

    /**
     * @var string
     * @isQueryParameter
     */
    protected $type;

    /**
     * The analyzer to use for the query string
     *
     * @var string
     * @isQueryParameter
     */
    public $analyzer;

    /**
     * Specify whether wildcard and prefix queries should be analyzed (default: false)
     *
     * @var boolean
     * @isQueryParameter
     */
    public $analyzeWildcard;

    /**
     * The default operator for query string query (AND or OR)
     *
     * @var string
     * @isQueryParameter
     */
    public $defaultOperator;

    /**
     * The field to use as default where no field prefix is given in the query string
     *
     * @var string
     * @isQueryParameter
     */
    public $df;

    /**
     * Specify whether to return detailed information about score computation as part of a hit
     *
     * @var boolean
     * @isQueryParameter
     */
    public $explain;

    /**
     * A comma-separated list of fields to return as part of a hit
     *
     * @var string
     * @isQueryParameter
     */
    public $fields;

    /**
     * Starting offset (default: 0)
     *
     * @var integer
     * @isQueryParameter
     */
    public $from;

    /**
     * Search operation type (default: query_then_fetch)
     *
     * @var string
     * @isQueryParameter
     */
    public $searchType;

    /**
     * Number of hits to return (default: 10)
     *
     * @var integer
     * @isQueryParameter
     */
    public $size;

    /**
     * A comma-separated list of <field>:<direction> pairs
     *
     * @var string
     * @isQueryParameter
     */
    public $sort;

    /**
     * Explicit operation timeout
     *
     * @var integer
     * @isQueryParameter
     */
    public $timeout;

    /**
     * Specify whether to return document version as part of a hit
     *
     * @var integer
     * @isQueryParameter
     */
    public $version;

    /**
     * The search definition using the Query DSL
     *
     * @var array
     * @isQueryParameter
     */
    public $body = [];

    public function __construct(DocumentRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->index = $repository->getIndex();
        $this->type = $repository->getType();
    }
}
