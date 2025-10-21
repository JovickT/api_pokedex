<?php

namespace App\Tests\Repository;

use App\Entity\Pokemon;
use App\Repository\PokemonRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PokemonRepositoryTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    public function testCreatePokemon(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $repository = $container->get(PokemonRepository::class);

        $pokemon = $repository->create('Pikachu', 'Electric');

        $this->assertInstanceOf(Pokemon::class, $pokemon);
        $this->assertEquals('Pikachu', $pokemon->getName());
        $this->assertEquals('Electric', $pokemon->getType());
        $this->assertNotNull($pokemon->getId());
    }
}
