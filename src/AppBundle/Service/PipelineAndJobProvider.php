<?php

namespace AppBundle\Service;

use AppBundle\Entity\Pipeline;
use AppBundle\Entity\Job;
use AppBundle\Repository\PipelineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Gitlab\Client;


class PipelineAndJobProvider
{

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var ProjectProvider */
    protected $projectProvider;

    /** @var PipelineRepository  */
    protected $pipelineRepository;

    /** @var Client */
    protected $gitlabClient;

    public function __construct(Client $gitlabClient, EntityManagerInterface $entityManager, ProjectProvider $projectProvider)
    {
        $this->entityManager = $entityManager;
        $this->projectProvider = $projectProvider;
        $this->gitlabClient = $gitlabClient;
        $this->pipelineRepository = $this->entityManager->getRepository(Pipeline::class);
    }

    public function findPipelinesWithJobs()
    {
        return $this->pipelineRepository->getPipelinesWithJobs();
    }


    /**
     * Create or update a pipeline
     * @param $request
     * @throws \Exception
     * @return object
     */
    public function handlePipeline($request)
    {
        if ($request->object_kind === "pipeline") {

            $pipelineId = $request->object_attributes->id;
            $projectId = $request->project->id;

            $project = $this->projectProvider->findOne($projectId);

            if ($project == null) {
                throw new Exception('Project not found!');
            }

            //this will throw exception if pipeline does not exist in project
            $this->gitlabClient->projects()->pipeline($projectId, $pipelineId);

            $pipeline = $this->entityManager->getRepository(Pipeline::Class)->findOneByPipelineId($pipelineId);
            if ($pipeline == null) {
                $pipeline = new Pipeline();
            }

            $status = $request->object_attributes->status;
            $duration = $request->object_attributes->duration;

            $pipeline->setPipelineId($pipelineId);
            $pipeline->setDuration($duration);
            $pipeline->setStatus($status);
            $pipeline->setProject($project);
            $this->entityManager->persist($pipeline);

            $builds = $request->builds;
            $jobIds = [];
            $stages = [];
            $jobStatus = [];
            foreach ($builds as $build) {
                $jobIds[] = $build->id;
                $stages[$build->id] = $build->stage;
                $jobStatus[$build->id] = $build->status;
            }

            foreach ($jobIds as $jobId) {
                $job = $this->entityManager->getRepository(Job::Class)->findOneByJobId($jobId);
                if ($job === null) {
                    $job = new Job();
                    $job->setJobId($jobId);
                }

                $job->setPipeline($pipeline);
                $job->setProject($project);
                $job->setStages($stages[$jobId]);
                $job->setStatus($jobStatus[$jobId]);

                $this->entityManager->persist($job);
            }
            $this->entityManager->flush();

            return $pipeline;
        }
    }

    /**
     * Create or update a job alias a build
     * @param $request
     * @throws \Exception
     * @return object
     */
    public function handleJob($request)
    {

        if ($request->object_kind === "build") {

            $projectId = $request->project_id;

            $project = $this->projectProvider->findOne($projectId);

            if ($project == null) {
                throw new Exception('Project not found!');
            }

            $jobId = $request->build_id;
            $job = $this->entityManager->getRepository(Job::class)->findOneByJobId($jobId);

            if ($job == null) {
                $job = new Job();
            }
            $job->setJobId($jobId);
            $stage = $request->build_stage;
            $job->setStages($stage);
            $status = $request->build_status;
            $job->setStatus($status);
            $job->setProject($project);

            $this->entityManager->persist($job);
            $this->entityManager->flush();

            return $job;
        }

    }

}