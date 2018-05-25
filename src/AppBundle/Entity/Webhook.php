<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Webhook
 *
 * @ORM\Table(name="webhook")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WebhookRepository")
 */
class Webhook
{
    /**
     * @var int
     *
     * @ORM\Id @ORM\Column(type="integer", name="webhook_id")
     */
    protected $webhookId;


    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    protected $url;

    /**
     *
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="webhooks")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="project_id")
     *
     */
    protected $project;

    /**
     * Set webhookId
     *
     * @param integer $webhookId
     *
     * @return Webhook
     */
    public function setWebhookId($webhookId)
    {
        $this->webhookId = $webhookId;

        return $this;
    }

    /**
     * Get webhookId
     *
     * @return int
     */
    public function getWebhookId()
    {
        return $this->webhookId;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Webhook
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
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

