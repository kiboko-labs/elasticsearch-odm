<?php

namespace Mosiyash\Elasticsearch;

class DocumentMapperTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->di->set('tests/document_mapper', $this->di->lazyNew('Mosiyash\Elasticsearch\DocumentMapper'));
        $this->di->params['Mosiyash\Elasticsearch\DocumentMapper']['document'] = $this->newCustomDocument();
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
        $mapper = $this->di->get('tests/document_mapper');

        $this->assertSame([], $mapper->getAliases());
    }

    public function testGetMapping()
    {
        $mapper = $this->di->get('tests/document_mapper');

        $expected = json_decode(
            '{
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
            }',
            true
        );

        $this->assertSame($expected, $mapper->getMapping());
    }

    public function testGetSettings()
    {
        $mapper = $this->di->get('tests/document_mapper');
        $settings = $mapper->getSettings();

        $this->assertNotEmpty($settings);
        $this->assertArrayHasKey('index', $settings);
        $this->assertInternalType('array', $settings['index']);
    }

    public function testGetWarmers()
    {
        $mapper = $this->di->get('tests/document_mapper');
        $warmers = $mapper->getWarmers();

        $this->assertSame([], $warmers);
    }

    /**
     * @return array
     */
    public function testValidateMapping()
    {
        $mapping = ['properties' => ['firstname' => ['type' => 'string', 'index' => 'not_analyzed']]];

        $documentStub = $this->getMockBuilder('Mosiyash\Elasticsearch\Tests\CustomDocument')
            ->disableOriginalConstructor()
            ->getMock();

        $documentStub->expects($this->any())
            ->method('getMapping')
            ->will($this->returnValue($mapping));

        $documentStub->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('custom'));

        $mapperStub = $this->getMockBuilder('Mosiyash\Elasticsearch\DocumentMapper')
            ->setConstructorArgs(['document' => $documentStub])
            ->setMethods(['getMapping'])
            ->getMock();

        $mapperStub->expects($this->any())
            ->method('getMapping')
            ->will($this->returnValue($mapping));

        $this->assertSame($documentStub->getMapping(), $mapperStub->getMapping());
        $this->assertTrue($mapperStub->validateMapping());

        return [
            $mapperStub,
            $documentStub,
        ];
    }

    /**
     * @depends testValidateMapping
     */
    public function testValidateMappingError(array $stubs)
    {
        list($mapperStub, $documentStub) = $stubs;

        $documentStub->expects($this->any())
            ->method('getMapping')
            ->will($this->returnValue(['invalid' => ['mapping' => 'info']]));

        $this->assertNotSame($documentStub->getMapping(), $mapperStub->getMapping());
        $this->assertFalse($mapperStub->validateMapping());
    }
}
