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
            $result = $this->getClient()->get($params);
        } catch (Missing404Exception $e) {
            return null;
        }

        $document = $this->newDocument();
        $document->fillThroughElasticsearchResponse($result);

        return $document;
    }

    /**
     * @param Search $params
     * @return array
     */
    public function findBy(Search $params)
    {
        $data = [];
        $result = $this->getClient()->search($params->asArray());

        foreach ($result['hits']['hits'] as $hit) {
            $document = $this->newDocument();
            $document->fillThroughElasticsearchResponse($hit);
            $data[] = $document;
        }

        return $data;
    }
}
