<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectRepository")
 */
class Project
{

    /**
     * @var int
     *
     * @ORM\Id @ORM\Column(name="project_id", type="integer")
     */
    protected $projectId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=false)
     */
    protected $deleted = false;


    /**
     *
     * @var Pipeline|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Pipeline", mappedBy="project")
     *
     */
    protected $pipelines;

    /**
     *
     * @var Job|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Job", mappedBy="project")
     *
     */
    protected $jobs;

    /**
     *
     * @var Webhook|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Webhook", mappedBy="project")
     *
     */
    protected $webhooks;

    /**
     *
     * @var Groups
     *
     * @ORM\ManyToOne(targetEntity="Groups", inversedBy="projects")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="group_id", onDelete="SET NULL")
     *
     */
    protected $group;


    public function __construct()
    {
        $this->pipelines = new ArrayCollection();
        $this->jobs = new ArrayCollection();
        $this->webhooks = new ArrayCollection();
    }

    /**
     * Set projectId
     *
     * @param integer $projectId
     *
     * @return Project
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * Get projectId
     *
     * @return int
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Project
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

    /**
     * Set group
     *
     * @param string $group
     *
     * @return Project
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Project
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Is delete
     *
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }


}

