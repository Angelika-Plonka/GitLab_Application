<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * Job
 *
 * @ORM\Table(name="job")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JobRepository")
 */
class Job
{
    /**
     * @var int
     *
     * @ORM\Id @ORM\Column(type="integer", name="job_id")
     * @Groups({"pipelinesWithJobs"})
     *
     */
    protected $jobId;


    /**
     * @var string
     *
     * @ORM\Column(name="stages", type="string", length=20)
     */
    protected $stages;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20)
     */
    protected $status;


    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="jobs")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="project_id")
     *
     */
    protected $project;


    /**
     * @var Pipeline
     *
     * @ORM\ManyToOne(targetEntity="Pipeline", inversedBy="jobs")
     * @ORM\JoinColumn(name="pipeline_id", referencedColumnName="pipeline_id", onDelete="SET NULL")
     *
     */
    protected $pipeline;



    /**
     * Set jobId
     *
     * @param integer $jobId
     *
     * @return Job
     */
    public function setJobId($jobId)
    {
        $this->jobId = $jobId;

        return $this;
    }

    /**
     * Get jobId
     *
     * @return int
     */
    public function getJobId()
    {
        return $this->jobId;
    }

    /**
     * Set stages
     *
     * @param string $stages
     *
     * @return Job
     */
    public function setStages($stages)
    {
        $this->stages = $stages;

        return $this;
    }

    /**
     * Get stages
     *
     * @return string
     */
    public function getStages()
    {
        return $this->stages;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Job
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
     * Set pipeline
     *
     * @param string $pipeline
     *
     * @return Job
     */
    public function setPipeline($pipeline)
    {
        $this->pipeline = $pipeline;

        return $this;
    }

    /**
     * Get pipeline
     *
     * @return string
     */
    public function getPipeline()
    {
        return $this->pipeline;
    }


    /**
     * Set project
     *
     * @param $project
     *
     * @return $this
     */
    public function setProject(Project $project) {

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

