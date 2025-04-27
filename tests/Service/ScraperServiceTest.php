<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/Service/ScraperService.php';

class ApiServiceTest extends TestCase
{
    protected $service;

    protected function setUp(): void
    {
        $this->service = new ScraperService();
    }

    public function testGetDataReturnsArray()
    {
        $result = $this->service->getData(1);

        $this->assertIsArray($result, "getData() debería retornar un array.");
        $this->assertNotEmpty($result, "getData(1) no debería retornar un array vacío.");
    }

    public function testGetDataHandlesInvalidPageGracefully()
    {
        $result = $this->service->getData(99999);

        $this->assertIsArray($result, "Incluso si falla, debe retornar un array.");
        if (isset($result['error'])) {
            $this->assertEquals('Error al consultar la página', $result['error'], "Debe retornar mensaje de error.");
        } else {
            $this->assertTrue(true, "La página existe, entonces se comportó correctamente.");
        }
    }

    public function testValidateReturnsExpectedText()
    {
        $dom = new DOMDocument();
        $dom->loadHTML('<span class="test">Hola mundo</span>');
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query("//span[@class='test']/text()");

        $reflection = new ReflectionClass(ScraperService::class);
        $method = $reflection->getMethod('validate');
        $method->setAccessible(true);

        $text = $method->invokeArgs($this->service, [$nodes]);

        $this->assertEquals("Hola mundo", $text, "validate() debería extraer el texto correctamente.");
    }

    public function testValidateHandlesEmptyNodes()
    {
        $reflection = new ReflectionClass(ScraperService::class);
        $method = $reflection->getMethod('validate');
        $method->setAccessible(true);

        $text = $method->invokeArgs($this->service, [null]);

        $this->assertEquals('', $text, "validate() debería devolver string vacío si el nodo es null.");
    }
}
