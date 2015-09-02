<?php

namespace Mosiyash\Elasticsearch;

use DocBlockReader\Reader;
use Doctrine\Common\Inflector\Inflector;

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
            $propertyName = Inflector::tableize($property->getName());
            if ($isQueryParameter === true && $this->{$property->getName()} !== null) {
                $data[$propertyName] = $this->{$property->getName()};
            }
        }

        return $data;
    }
}
