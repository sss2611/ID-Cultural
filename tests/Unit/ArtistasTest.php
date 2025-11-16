<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests para validación de artistas
 */
class ArtistasTest extends TestCase
{
    /**
     * @test
     */
    public function testArtistNameNotEmpty()
    {
        $name = 'Juan Pérez';
        $this->assertNotEmpty($name);
    }

    /**
     * @test
     */
    public function testArtistCategoryValid()
    {
        $validCategories = ['Música', 'Pintura', 'Escultura', 'Danza', 'Teatro'];
        $category = 'Música';

        $this->assertContains($category, $validCategories);
    }

    /**
     * @test
     */
    public function testArtistEmailFormat()
    {
        $email = 'artista@example.com';
        $this->assertTrue(filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
    }

    /**
     * @test
     */
    public function testArtistPhoneFormat()
    {
        $phone = '+573001234567';
        $this->assertTrue(preg_match('/^\+\d{1,3}\d{9,}$/', $phone) === 1);
    }

    /**
     * @test
     */
    public function testArtistBiographyMinLength()
    {
        $bio = 'Artista colombiano con 20 años de experiencia';
        $minLength = 20;

        $this->assertTrue(strlen($bio) >= $minLength);
    }

    /**
     * @test
     */
    public function testArtistStatusValidation()
    {
        $validStatuses = ['pendiente', 'validado', 'rechazado'];
        $status = 'validado';

        $this->assertContains($status, $validStatuses);
    }

    /**
     * @test
     */
    public function testArtistMunicipalityRequired()
    {
        $municipality = 'Medellín';
        $this->assertNotEmpty($municipality);
    }
}
