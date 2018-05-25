<?php

namespace AppBundle\Service;

use Gitlab\Client;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Webhook;
use Symfony\Component\HttpFoundation\Session\Session;

class WebhookProvider
{

    /** @var ProjectProvider */
    protected $projectProvider;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var Client */
    protected $gitlabClient;

    /** @var string */
    protected $webhookURL;

    /** @var Session */
    protected $session;

    public function __construct(EntityManagerInterface $entityManager, ProjectProvider $projectProvider, Client $gitlabClient, string $webhookURL, Session $session)
    {
        $this->entityManager = $entityManager;
        $this->projectProvider = $projectProvider;
        $this->gitlabClient = $gitlabClient;
        $this->webhookURL = $webhookURL;
        $this->session = $session;
    }

    /**
     * @param $projectId
     * @throws \Exception
     */
    public function addToProject($projectId)
    {
        $project = $this->projectProvider->findOne($projectId);

        $events = [
            'pipeline_events' => true,
            'job_events' => true
        ];

        $webhook = $this->gitlabClient->projects()->addHook($projectId, $this->webhookURL, $events);
        $hook = new Webhook();
        $hook->setWebhookId($webhook['id']);
        $hook->setUrl($webhook['url']);
        $hook->setProject($project);
        $this->entityManager->persist($hook);
        $this->entityManager->flush();
    }

    /**
     * @param $projectId
     */
    public function removeFromProject($projectId)
    {
        $webhooks = $this->gitlabClient->projects()->hooks($projectId);

        if ($webhooks == null) {
            $this->session->getFlashBag()->add(
                'error',
                'Not found webhook for this project!'
            );
        } else {
            foreach ($webhooks as $webhook) {
                $this->gitlabClient->projects()->removeHook($projectId, $webhook['id']);
                $hook = $this->entityManager->getRepository(Webhook::class)->findOneByWebhookId($webhook['id']);
                $this->entityManager->remove($hook);
            }
            $this->entityManager->flush();

            if (count($webhooks) === 1) {
                $this->session->getFlashBag()->add(
                    'notice',
                    'The webhook was removed from project!'
                );
            }else{
                $this->session->getFlashBag()->add(
                    'notice',
                    'All webhooks was removed from project!'
                );
            }
        }
    }
}