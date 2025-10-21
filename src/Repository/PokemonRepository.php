<?php

namespace App\Repository;

use App\Entity\Pokemon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pokemon>
 */
class PokemonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pokemon::class);
    }

   public function create(string $name, string $type): Pokemon
    {
        $pokemon = new Pokemon();
        $pokemon->setName($name);
        $pokemon->setType($type);
        $pokemon->setState(false); // optionnel mais propre

        $em = $this->getEntityManager();
        $em->persist($pokemon);
        $em->flush();

        return $pokemon;
    }

    public function save(Pokemon $pokemon, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        if ($flush) {
            $em->persist($pokemon);
            $em->flush();
        }
    }

   public function remove(Pokemon $pokemon, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->remove($pokemon);

        if ($flush) {
            $em->flush();
        }
    }







//    /**
//     * @return Pokemon[] Returns an array of Pokemon objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Pokemon
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
