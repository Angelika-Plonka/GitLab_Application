<?php

namespace AppBundle\Repository;
use AppBundle\Entity\Groups;

/**
 * GroupsRepository
 *
 */
class GroupsRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Get a list of groups by group id
     *
     * @param array $groupIds
     * @return Groups[]
     */
    public function findByGroupIds(array $groupIds): array
    {
        $qb = $this->createQueryBuilder('g');
        $qb->select('g')
            ->andWhere($qb->expr()->in('g.groupId', ':groupIds'))
            ->setParameter('groupIds', $groupIds);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get a list of removed groups by group id
     *
     * @param array $groupIds, int $min, int $max
     * @return Groups[]
     */
    public function findRemovedGroups(array $groupIds, int $min, int $max): array
    {
        $qb = $this->createQueryBuilder('g');
        $qb->select('g')
            ->andWhere($qb->expr()->notIn('g.groupId', ':groupIds'))
            ->andWhere('g.groupId >= :min')
            ->andWhere('g.groupId <= :max')
            ->setParameters([
                'groupIds' => $groupIds,
                'min' => $min,
                'max' => $max
            ]);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneOrNull($id)
    {
        $qb = $this->createQueryBuilder('g');
        $qb->select('g')
            ->andWhere('g.groupId = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
