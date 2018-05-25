<?php

namespace AppBundle\Service;

use AppBundle\Entity\Project;
use AppBundle\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Service\GroupProvider;

class ProjectProvider
{

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var ProjectRepository */
    protected $projectRepository;

    /** @var GroupProvider */
    protected $groupProvider;

    public function __construct(EntityManagerInterface $entityManager, GroupProvider $groupProvider)
    {
        $this->entityManager = $entityManager;
        $this->projectRepository = $this->entityManager->getRepository(Project::class);
        $this->groupProvider = $groupProvider;
    }

    public function findAll()
    {
        return $this->projectRepository->getAll();
    }


    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOne($id)
    {
        return $this->projectRepository->findOneOrNull($id);
    }


    /**
     * @param $projectsMap
     * @return array
     */
    public function createBatch($projectsMap)
    {
        $projects = [];
        foreach ($projectsMap as $id => $value) {
            $project = new Project();
            $project->setName($value['name']);
            $project->setProjectId($value['id']);
            $projects[] = $project;
            $this->entityManager->persist($project);
        }

        return $projects;
    }


    /**
     * @param $projectsMap
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function synchronize($projectsMap)
    {
        $ids = [];

        foreach ($projectsMap as $id => $value) {
            $ids[] = $id;
        }


        $minId = min($ids);
        $maxId = max($ids);

        $existingProjects = $this->projectRepository->findByProjectIds($ids);

        $projectsToRemove = $this->projectRepository->findRemovedProject($ids, $minId, $maxId);


        foreach ($projectsToRemove as $projectToRemove) {
            $projectToRemove->setDeleted(true);
        }

        foreach ($existingProjects as $existingProject) {
            $projectId = $existingProject->getProjectId();
            $groupProduct = $existingProject->getGroup();
            $groupData = $projectsMap[$projectId]['namespace'];

            if(null === $groupProduct || $groupProduct->getGroupId() !== $groupData['id']) {
                $currentGroup = $this->groupProvider->findOne($groupData['id']);
                if(null === $currentGroup) {
                    $currentGroup = $this->groupProvider->create($groupData);
                }
                $existingProject->setGroup($currentGroup);
            }
            unset($projectsMap[$projectId]);
        }


        $this->createBatch($projectsMap);
        $this->entityManager->flush();
    }

    public function flushAll() {
        $this->entityManager->flush();
    }
}
