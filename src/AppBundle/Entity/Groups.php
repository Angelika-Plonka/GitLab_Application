<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Groups
 *
 * @ORM\Table(name="groups")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GroupsRepository")
 */
class Groups
{

    /**
     * @var int
     *
     * @ORM\Id @ORM\Column(type="integer", name="group_id")
     */
    protected $groupId;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     *
     * @var Project|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Project", mappedBy="group")
     *
     */
    protected $projects;


    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }



    /**
     * Set groupId
     *
     * @param integer $groupId
     *
     * @return Groups
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return Groups
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}
