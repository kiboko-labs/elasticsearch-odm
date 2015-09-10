<?php

namespace Mosiyash\Elasticsearch;

use Mosiyash\Elasticsearch\Tests\CustomDocument;

class DocumentMapperTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->di->set('tests/document_mapper', $this->di->lazyNew('Mosiyash\Elasticsearch\DocumentMapper'));
        $this->di->params['Mosiyash\Elasticsearch\DocumentMapper']['document'] = $this->newCustomDocument();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     * @throws \Aura\Di\Exception\ServiceNotFound
     */
    public function getDocumentStub()
    {
        $mapping = [
            'properties' => [
                'firstname' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
                'lastname' => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            ],
        ];

        $documentStub = $this->getMockBuilder('Mosiyash\Elasticsearch\Tests\CustomDocument')
            ->disableOriginalConstructor()
            ->getMock();

        $documentStub->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->di->get('tests/documents:custom_repository')));

        $documentStub->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('custom'));

        $documentStub->expects($this->any())
            ->method('getMapping')
            ->will($this->returnValue($mapping));

        return $documentStub;
    }

    /**
     * @param CustomDocument $documentStub
     * @param null|array $mapping
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getMapperStub(CustomDocument $documentStub, array $mapping = null)
    {
        if (is_null($mapping)) {
            $mapping = $documentStub->getMapping();
        }

        $mapperStub = $this->getMockBuilder('Mosiyash\Elasticsearch\DocumentMapper')
            ->setConstructorArgs(['document' => $documentStub])
            ->setMethods(['getMapping'])
            ->getMock();

        $mapperStub->expects($this->any())
            ->method('getMapping')
            ->will($this->returnValue($mapping));

        return $mapperStub;
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

    public function testValidateMapping()
    {
        $documentStub = $this->getDocumentStub();
        $mapperStub = $this->getMapperStub($documentStub);

        $this->assertSame($documentStub->getMapping(), $mapperStub->getMapping());
        $this->assertTrue($mapperStub->validateMapping());
    }

    public function testValidateMappingError()
    {
        $documentStub = $this->getDocumentStub();
        $mapperStub = $this->getMapperStub($documentStub, ['invalid' => ['mapping' => 'info']]);

        $this->assertNotSame($documentStub->getMapping(), $mapperStub->getMapping());
        $this->assertFalse($mapperStub->validateMapping());
    }

    public function testUpdateMapping()
    {
        $documentStub = $this->getDocumentStub();
        $mapperStub = $this->getMapperStub($documentStub, ['invalid' => ['mapping' => 'info']]);

        $this->assertTrue($mapperStub->updateMapping());

        $mapping = $documentStub->getRepository()->getClient()->indices()->getMapping([
            'index' => $documentStub->getRepository()->getIndex(),
            'type' => $documentStub->getRepository()->getType(),
        ]);

        $this->assertSame(
            $documentStub->getMapping(),
            $mapping[$documentStub->getRepository()->getIndex()]['mappings'][$documentStub->getRepository()->getType()]
        );
    }
}
