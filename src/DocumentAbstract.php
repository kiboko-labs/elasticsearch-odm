<?php

namespace Mosiyash\ElasticSearch;

use Aura\Di\Container;
use DocBlockReader\Reader;
use Elasticsearch\Client;
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
     * @var string
     */
    private $clientServiceName;

    /**
     * @var string
     */
    private $repositoryServiceName;

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
     * @param string $clientServiceName
     */
    public function setClientServiceName($clientServiceName)
    {
        $this->clientServiceName = $clientServiceName;
    }

    /**
     * @param string $repositoryServiceName
     */
    public function setRepositoryServiceName($repositoryServiceName)
    {
        $this->repositoryServiceName = $repositoryServiceName;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->di->get($this->clientServiceName);
    }

    /**
     * @return DocumentRepositoryInterface
     */
    public function getRepository()
    {
        return $this->di->get($this->repositoryServiceName);
    }

    public function getIndex()
    {
        return $this->getRepository()->getIndex();
    }

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

    public function get()
    {

    }

    /**
     * @return array
     */
    public function save()
    {
        $client = $this->getClient();

        if ($this->isNew()) {
            $params = [];
            $body = $this->getBody();

            $params['index'] = $this->getIndex();
            $params['type'] = $this->getType();

            if (array_key_exists('id', $body)) {
                $params['id'] = $body['id'];
                unset($body['id']);
            }

            $params['body'] = $body;

            $result = $client->create($params);

            if (array_key_exists('created', $result) && $result['created'] === true) {
                $this->isNew = false;
                $this->id = $result['_id'];
            }

            return $result;
        }

        return [];
    }
}
