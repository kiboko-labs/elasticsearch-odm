<?php

namespace Mosiyash\Elasticsearch;

abstract class QueryParamsDocumentAbstract
{
    use QueryParamsTrait;

    /**
     * @var DocumentInterface
     */
    protected $document;

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

    public function __construct(DocumentInterface $document)
    {
        $this->document = $document;
        $this->index = $document->getIndex();
        $this->type = $document->getType();
        $this->id = $document->id;
    }
}
