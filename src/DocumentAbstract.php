<?php

namespace Mosiyash\ElasticSearch;

use Aura\Di\Container;
use DocBlockReader\Reader;
use Mosiyash\ElasticSearch\Exceptions\LogicException;

/**
 * Class DocumentAbstract
 *
 * @package Mosiyash\ElasticSearch
 * @Parameter-read Container $di
 */
abstract class DocumentAbstract implements DocumentInterface
{
    /**
     * @var Container
     */
    public $di;

    public $queryParams;

    /**
     * @param Container $di
     */
    public function setDi(Container $di)
    {
        if (!is_null($this->di)) {
            throw new LogicException('The container is already bound');
        }

        $this->di = $di;
    }

    /**
     * @return array
     */
    public function getBody()
    {
        $data = [];
        $class = get_class($this);
        $reflection = new \ReflectionClass($class);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $reader = new Reader($class, $property->getName(), 'property');
            $isBodyParameter = $reader->getParameter('isBodyParameter');
            if ($isBodyParameter === true) {
                $data[$property->getName()] = $this->{$property->getName()};
            }
        }

        return $data;
    }
}
