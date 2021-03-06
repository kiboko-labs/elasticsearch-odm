<?php

namespace Mosiyash\Elasticsearch;

use Aura\Di\Container;
use Codeliner\ArrayReader\ArrayReader;
use DocBlockReader\Reader;
use Elasticsearch\Client;
use Mosiyash\Elasticsearch\Exceptions\InvalidArgumentException;
use Mosiyash\Elasticsearch\Exceptions\LogicException;
use Mosiyash\Elasticsearch\QueryParams\Create;
use Mosiyash\Elasticsearch\QueryParams\Delete;
use Mosiyash\Elasticsearch\QueryParams\Update;

/**
 * Class DocumentAbstract
 *
 * @package Mosiyash\Elasticsearch
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
     */
    public $id;

    /**
     * @var integer
     */
    public $version;

    /**
     * @param Container $di
     */
    final public function setDi(Container $di)
    {
        if (!is_null($this->di)) {
            throw new LogicException('The container is already bound');
        }

        $this->di = $di;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->getRepository()->getClient();
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->getRepository()->getIndex();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getRepository()->getType();
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
     * @param array $result
     */
    public function fillThroughElasticsearchResponse(array $result)
    {
        $arrayReader = new ArrayReader($result);

        $this->isNew = false;
        $this->id = $arrayReader->stringValue('_id');
        $this->version = $arrayReader->mixedValue('_version', null);

        $class = get_class($this);
        $reflection = new \ReflectionClass($class);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            if ($property->getName() !== 'id') {
                $reader = new Reader($class, $property->getName(), 'property');
                $isBodyParameter = $reader->getParameter('isBodyParameter');
                if ($isBodyParameter === true) {
                    $this->{$property->getName()} = $arrayReader->mixedValue('_source.'.$property->getName());
                }
            }
        }
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

    /**
     * Create or update the document.
     *
     * @return array
     */
    public function save()
    {
        $client = $this->getClient();

        if ($this->isNew()) {
            $params = new Create($this);
            $params->body = $this->getBody();

            $result = $client->create($params->asArray());

            if (array_key_exists('created', $result) && $result['created'] === true) {
                $this->isNew = false;
                $this->id = $result['_id'];
                $this->version = $result['_version'];
            }

            return $result;
        } else {
            $params = new Update($this);
            $params->body['doc'] = $this->getBody();

            $result = $client->update($params->asArray());
            $this->version = $result['_version'];

            return $result;
        }
    }

    /**
     * @return array
     */
    public function delete()
    {
        $client = $this->getClient();
        $params = new Delete($this);
        $result = $client->delete($params->asArray());

        return $result;
    }
}
