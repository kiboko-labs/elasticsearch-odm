<?php

namespace Mosiyash\Elasticsearch;

class MapperTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->di->set('tests/mapper', $this->di->lazyNew('Mosiyash\Elasticsearch\Mapper'));
        $this->di->params['Mosiyash\Elasticsearch\Mapper']['document'] = $this->newCustomDocument();
    }

    /**
     * @return CustomDocument
     */
    protected function newCustomDocument()
    {
        $document = $this->di->get('tests/documents:custom')->__invoke();

        return $document;
    }

    public function testGetAliases()
    {
        $mapper = $this->di->get('tests/mapper');

        $this->assertSame([], $mapper->getAliases());
    }

    public function testGetMappings()
    {
        $mapper = $this->di->get('tests/mapper');

        $expected = json_decode(
            '{
              "custom" : {
                "properties" : {
                    "firstname" : {
                        "type" : "string",
                        "index" : "not_analyzed"
                  },
                  "lastname" : {
                        "type" : "string",
                        "index" : "not_analyzed"
                  }
                }
              }
            }',
            true
        );

        $this->assertSame($expected, $mapper->getMappings());
    }

    public function testGetSettings()
    {
        $mapper = $this->di->get('tests/mapper');
        $settings = $mapper->getSettings();

        $this->assertNotEmpty($settings);
        $this->assertArrayHasKey('index', $settings);
        $this->assertInternalType('array', $settings['index']);
    }

    public function testGetWarmers()
    {
        $mapper = $this->di->get('tests/mapper');
        $warmers = $mapper->getWarmers();

        $this->assertSame([], $warmers);
    }

    public function testCheck()
    {
        $mapper = $this->di->get('tests/mapper');
    }
}
