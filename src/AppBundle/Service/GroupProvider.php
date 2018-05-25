<?php

namespace AppBundle\Service;

use AppBundle\Entity\Groups;
use AppBundle\Repository\GroupsRepository;
use Doctrine\ORM\EntityManagerInterface;

class GroupProvider
{

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var  GroupsRepository */
    protected $groupsRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->groupsRepository = $this->entityManager->getRepository(Groups::class);
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOne($id)
    {
        return $this->groupsRepository->findOneOrNull($id);
    }

    /**
     * @param $namespaceData
     * @return Groups
     */
    public function create($namespaceData)
    {
            $group = new Groups();
            $group->setGroupId($namespaceData['id']);
            $group->setName($namespaceData['name']);
            $this->entityManager->persist($group);

            return $group;
    }

    /**
     * @param $namespacesData
     * @return array
     */
    public function createGroups($namespacesData)
    {
        $namespaces = [];
        foreach ($namespacesData as $namespaceData) {
            $namespaces[] = $this->create($namespaceData);
        }

        return $namespaces;
    }

    /**
     * @param $namespacesData
     */
    public function synchronize($namespacesData)
    {
        $ids = [];

        foreach ($namespacesData as $id => $value) {
            $ids[] = $id;
        }

        $minId = min($ids);
        $maxId = max($ids);

        $existingGroups = $this->groupsRepository->findByGroupIds($ids);

        $groupsToRemove = $this->groupsRepository->findRemovedGroups($ids, $minId, $maxId);

        foreach ($groupsToRemove as $groupToRemove) {
            $this->entityManager->remove($groupToRemove);
        }

        foreach ($existingGroups as $existingGroup) {
            $groupId = $existingGroup->getGroupId();
            unset($namespacesData[$groupId]);
        }

        $this->createGroups($namespacesData);
        $this->entityManager->flush();
    }
}
