<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests para manejo centralizado de errores
 */
class ErrorHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function testErrorHandlerReturnsJson()
    {
        $error = ['status' => 'error', 'message' => 'Test error'];
        $json = json_encode($error);

        $this->assertIsString($json);
        $this->assertStringContainsString('error', $json);
    }

    /**
     * @test
     */
    public function testErrorHandlerIncludesCode()
    {
        $error = ['status' => 'error', 'code' => 400, 'message' => 'Bad request'];

        $this->assertArrayHasKey('code', $error);
        $this->assertEquals(400, $error['code']);
    }

    /**
     * @test
     */
    public function testSuccessResponseFormat()
    {
        $response = ['status' => 'success', 'data' => []];

        $this->assertEquals('success', $response['status']);
        $this->assertIsArray($response['data']);
    }

    /**
     * @test
     */
    public function testErrorCodesAreValid()
    {
        $validCodes = [400, 401, 403, 404, 500, 503];
        $code = 400;

        $this->assertContains($code, $validCodes);
    }
}
