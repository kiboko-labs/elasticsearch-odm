<?php

namespace Mosiyash\ElasticSearch;

use DocBlockReader\Reader;

abstract class QueryParamsAbstract
{
    private $document;

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
     * @return DocumentInterface
     */
    public function getDocument()
    {
        return $this->document;
    }

    public function __construct(DocumentInterface $document)
    {
        $this->document = $document;
        $this->index = $document->getIndex();
        $this->type = $document->getType();
    }

    public function asArray()
    {
        $data = [];
        $class = get_class($this);
        $reflection = new \ReflectionClass($class);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $reader = new Reader($class, $property->getName(), 'property');
            $isQueryParameter = $reader->getParameter('isQueryParameter');
            if ($isQueryParameter === true && $this->{$property->getName()} !== null) {
                $data[$property->getName()] = $this->{$property->getName()};
            }
        }

        return $data;
    }
}
