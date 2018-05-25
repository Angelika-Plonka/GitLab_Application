<?php

namespace AppBundle\Repository;
use AppBundle\Entity\Groups;
use AppBundle\Entity\Project;

/**
 * ProjectRepository
 *
 */
class ProjectRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Get a list of projects by project id
     *
     * @param array $projectIds
     * @return Project[]
     */
    public function findByProjectIds(array $projectIds): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p', 'g')
            ->andWhere($qb->expr()->in('p.projectId', ':projectIds'))
            ->orderBy('p.projectId', 'ASC')
            ->leftJoin('p.group', 'g')
            ->setParameter('projectIds', $projectIds);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get a list of projects with associated groups
     *
     * @return Project[]
     */
    public function getAll(): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p', 'g')
            ->leftJoin('p.group', 'g')
            ->orderBy('p.projectId', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Get a list of removed projects by project id
     *
     * @param array $projectIds, int $min, int $max
     * @return Project[]
     */
    public function findRemovedProject(array $projectIds, int $min, int $max): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p')
            ->andWhere($qb->expr()->notIn('p.projectId', ':projectIds'))
            ->andWhere('p.projectId >= :min')
            ->andWhere('p.projectId <= :max')
            ->orderBy('p.projectId', 'ASC')
            ->setParameters([
                'projectIds' => $projectIds,
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
        $qb = $this->createQueryBuilder('p');
        $qb->select('p')
            ->andWhere('p.projectId = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}