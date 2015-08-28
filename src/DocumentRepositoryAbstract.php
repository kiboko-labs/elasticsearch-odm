<?php

namespace Mosiyash\ElasticSearch;

use Aura\Di\Container;
use Mosiyash\ElasticSearch\Exceptions\LogicException;

abstract class DocumentRepositoryAbstract implements DocumentRepositoryInterface
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
    private $documentClassName;

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
     * @param string $documentClassName
     */
    public function setDocumentClassName($documentClassName)
    {
        $this->documentClassName = $documentClassName;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->di->get($this->clientServiceName);
    }

    /**
     * @param string $id
     */
    public function findOneById($id)
    {
        $params = [
            'index' => $this->getIndex(),
            'type' => $this->getType(),
            'id' => $id,
        ];

        $result = $this->getClient()->get($params);

        if (!$result) {
            return NULL;
        }

        $document = $this->di->newInstance($this->documentClassName);
        $document->fillThroughElasticsearchResponse($result);

        return $document;
    }
}