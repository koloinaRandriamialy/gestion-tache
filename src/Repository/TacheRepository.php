<?php

namespace App\Repository;

use App\Entity\Tache;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tache>
 */
class TacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tache::class);
    }

    /**
     * @return Tache[] Toutes les tâches, triées par priorité puis par date de création
     */
    public function findAllOrderByPriorite(): array
    {
        return $this->createQueryBuilder('t')
            ->addSelect("CASE 
                WHEN t.priorite = 'urgente' THEN 1
                WHEN t.priorite = 'haute' THEN 2
                WHEN t.priorite = 'moyenne' THEN 3
                WHEN t.priorite = 'basse' THEN 4
                ELSE 5 END AS HIDDEN priorite_ordre")
            ->orderBy('priorite_ordre', 'ASC')
            ->addOrderBy('t.dataCreation', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Tache[] Tâches assignées à un utilisateur donné, triées par priorité puis par date de création
     */
    public function findByAssigneA(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.assigneA = :user')
            ->setParameter('user', $user)
            ->addSelect("CASE 
                WHEN t.priorite = 'urgente' THEN 1
                WHEN t.priorite = 'haute' THEN 2
                WHEN t.priorite = 'moyenne' THEN 3
                WHEN t.priorite = 'basse' THEN 4
                ELSE 5 END AS HIDDEN priorite_ordre")
            ->orderBy('priorite_ordre', 'ASC')
            ->addOrderBy('t.dataCreation', 'ASC')
            ->getQuery()
            ->getResult();
    }
}