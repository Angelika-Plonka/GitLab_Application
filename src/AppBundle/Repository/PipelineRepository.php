<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Pipeline;

/**
 * PipelineRepository
 *
 */
class PipelineRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Get a list of jobs with associated pipelines
     *
     * @return Pipeline[]
     */
    public function getPipelinesWithJobs(): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select(['p', 'j'])
            ->innerJoin('p.jobs', 'j')
            ->orderBy('p.pipelineId', 'ASC')
            ->orderBy('j.jobId', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
