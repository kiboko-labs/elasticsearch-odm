<?php

namespace Mosiyash\ElasticSearch;

use DocBlockReader\Reader;

trait QueryParamsTrait
{
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
