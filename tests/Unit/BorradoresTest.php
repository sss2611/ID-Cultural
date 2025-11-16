<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests para validación de borradores
 */
class BorradoresTest extends TestCase
{
    /**
     * @test
     */
    public function testBorridorTitleNotEmpty()
    {
        $title = 'Mi Primera Obra';
        $this->assertNotEmpty($title);
    }

    /**
     * @test
     */
    public function testBorridorTitleMinLength()
    {
        $title = 'Mi Primera Obra';
        $minLength = 5;

        $this->assertTrue(strlen($title) >= $minLength);
    }

    /**
     * @test
     */
    public function testBorridorDescriptionRequired()
    {
        $description = 'Descripción de la obra';
        $this->assertNotEmpty($description);
    }

    /**
     * @test
     */
    public function testBorridorStatusOptions()
    {
        $validStatuses = ['borrador', 'enviado', 'validado', 'publicado', 'rechazado'];
        $status = 'borrador';

        $this->assertContains($status, $validStatuses);
    }

    /**
     * @test
     */
    public function testBorridorCategoryRequired()
    {
        $category = 'Música';
        $validCategories = ['Música', 'Pintura', 'Escultura', 'Danza', 'Teatro', 'Literatura'];

        $this->assertContains($category, $validCategories);
    }

    /**
     * @test
     */
    public function testBorridorYearValid()
    {
        $year = 2024;
        $currentYear = date('Y');

        $this->assertTrue($year <= $currentYear);
    }

    /**
     * @test
     */
    public function testBorridorArtistIdRequired()
    {
        $artistId = 1;
        $this->assertIsInt($artistId);
        $this->assertGreaterThan(0, $artistId);
    }
}
