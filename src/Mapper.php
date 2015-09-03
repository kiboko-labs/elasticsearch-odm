<?php

namespace Mosiyash\Elasticsearch;

class Mapper
{
    /**
     * @var DocumentInterface
     */
    public $document;

    /**
     * @param DocumentInterface $document
     */
    public function __construct(DocumentInterface $document)
    {
        $this->document = $document;
    }

    /**
     * @return array
     */
    final public function getAliases()
    {
        $client = $this->document->getRepository()->getClient();

        $aliases = $client->indices()->getAliases([
            'index' => $this->document->getRepository()->getIndex(),
            'name' => $this->document->getRepository()->getType(),
        ]);

        return isset($aliases[$this->document->getRepository()->getIndex()]['aliases'])
            ? $aliases[$this->document->getRepository()->getIndex()]['aliases']
            : [];
    }

    /**
     * @return array
     */
    final public function getMappings()
    {
        $client = $this->document->getRepository()->getClient();

        $mappings = $client->indices()->getMapping([
            'index' => $this->document->getRepository()->getIndex(),
            'type' => $this->document->getRepository()->getType(),
        ]);

        return isset($mappings[$this->document->getRepository()->getIndex()]['mappings'])
            ? $mappings[$this->document->getRepository()->getIndex()]['mappings']
            : [];
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        $client = $this->document->getRepository()->getClient();

        $settings = $client->indices()->getSettings([
            'index' => $this->document->getRepository()->getIndex(),
        ]);

        return isset($settings[$this->document->getRepository()->getIndex()]['settings'])
            ? $settings[$this->document->getRepository()->getIndex()]['settings']
            : [];
    }

    /**
     * @return array
     */
    public function getWarmers()
    {
        $client = $this->document->getRepository()->getClient();

        $warmers = $client->indices()->getWarmer([
            'index' => $this->document->getRepository()->getIndex(),
        ]);

        return isset($warmers[$this->document->getRepository()->getIndex()]['warmers'])
            ? $warmers[$this->document->getRepository()->getIndex()]['warmers']
            : [];
    }
}
