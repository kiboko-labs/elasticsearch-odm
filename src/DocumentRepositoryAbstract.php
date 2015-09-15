<?php

namespace Mosiyash\Elasticsearch;

use Aura\Di\Container;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Mosiyash\Elasticsearch\Exceptions\LogicException;
use Mosiyash\Elasticsearch\QueryParams\Search;

abstract class DocumentRepositoryAbstract implements DocumentRepositoryInterface
{
    /**
     * @var Container
     */
    public $di;

    /**
     * @var null|array
     */
    private $lastResult;

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
     * @return array|null
     */
    public function getLastResult()
    {
        return $this->lastResult;
    }

    /**
     * @param string $id
     * @return null|DocumentInterface
     */
    public function find($id)
    {
        $params = [
            'index' => $this->getIndex(),
            'type' => $this->getType(),
            'id' => $id,
        ];

        try {
            $this->lastResult = $this->getClient()->get($params);
        } catch (Missing404Exception $e) {
            return null;
        }

        $document = $this->newDocument();
        $document->fillThroughElasticsearchResponse($this->lastResult);

        return $document;
    }

    /**
     * @param Search $params
     * @return array
     */
    public function findBy(Search $params)
    {
        $data = [];
        $this->lastResult = $this->getClient()->search($params->asArray());

        foreach ($this->lastResult['hits']['hits'] as $hit) {
            $document = $this->newDocument();
            $document->fillThroughElasticsearchResponse($hit);
            $data[] = $document;
        }

        return $data;
    }
}
