<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;

/**
 * Pipeline
 *
 * @ORM\Table(name="pipeline")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PipelineRepository")
 */
class Pipeline
{

    /**
     * @var int
     *
     * @ORM\Id @ORM\Column(name="pipeline_id", type="integer")
     * @JMS\Groups({"pipelinesWithJobs"})
     *
     */
    protected $pipelineId;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    protected $status;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="integer")
     */
    protected $duration;

    /**
     *
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="pipelines")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="project_id")
     *
     */
    protected $project;


    /**
     * @var Job||ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Job", mappedBy="pipeline")
     * @JMS\Groups({"pipelinesWithJobs"})
     * @JMS\MaxDepth(2)
     *
     */
    protected $jobs;


    public function __construct()
    {
        $this->jobs = new ArrayCollection();
    }

    /**
     * Set pipelineId
     *
     * @param integer $pipelineId
     *
     * @return Pipeline
     */
    public function setPipelineId($pipelineId)
    {
        $this->pipelineId = $pipelineId;

        return $this;
    }

    /**
     * Get pipelineId
     *
     * @return int
     */
    public function getPipelineId()
    {
        return $this->pipelineId;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Pipeline
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return Pipeline
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set project
     *
     * @param $project
     *
     * @return $this
     */
    public function setProject(Project $project)
    {

        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }
}

