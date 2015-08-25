<?php

namespace Mosiyash\ElasticSearch;

use Aura\Di\Container;
use DocBlockReader\Reader;
use Mosiyash\ElasticSearch\Exceptions\InvalidArgumentException;
use Mosiyash\ElasticSearch\Exceptions\LogicException;

/**
 * Class DocumentAbstract
 *
 * @package Mosiyash\ElasticSearch
 */
abstract class DocumentAbstract implements DocumentInterface
{
    /**
     * @var Container
     */
    public $di;

    /**
     * @var bool
     */
    private $isNew = true;

    /**
     * @var string
     * @isBodyParameter
     */
    public $id;

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
     * @return boolean
     */
    public function isNew()
    {
        return $this->isNew;
    }

    /**
     * @param boolean $isNew
     */
    final private function setIsNew($isNew)
    {
        if (!is_bool($isNew)) {
            throw new InvalidArgumentException('Value must be a boolen type');
        }

        $this->isNew = $isNew;
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

        if ((string) $data['id'] === '') {
            unset($data['id']);
        }

        return $data;
    }
}
