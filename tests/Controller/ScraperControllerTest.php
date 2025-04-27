<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/Controller/ScraperController.php';

class ApiControllerTest extends TestCase
{
    protected $controller;

    protected function setUp(): void
    {
        $this->controller = new ScraperController();
    }

    public function testHandleRequestReturnsJson()
    {
        ob_start();
        $this->controller->handleRequest('/1');
        $output = ob_get_clean();

        $this->assertJson($output, "La salida debe ser JSON válido.");
        $decoded = json_decode($output, true);

        $this->assertIsArray($decoded, "El JSON decodificado debe ser un array.");
    }

    public function testHandleRequestReturns404ForInvalidRoute()
    {
        ob_start();
        $this->controller->handleRequest('/invalid/route');
        $output = ob_get_clean();

        $this->assertJson($output, "Incluso errores deben retornar JSON válido.");
        $decoded = json_decode($output, true);

        $this->assertArrayHasKey('error', $decoded, "Debe tener clave 'error' en la respuesta.");
        $this->assertEquals('Endpoint no encontrado', $decoded['error']);
    }
}

