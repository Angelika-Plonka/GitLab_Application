<?php

namespace Tests\Unit\AppBundle\Service;

use AppBundle\Entity\Job;
use AppBundle\Entity\Pipeline;
use AppBundle\Entity\Project;
use Doctrine\ORM\EntityManager;
use Fidry\AliceDataFixtures\Loader\PurgerLoader;
use Gitlab\Api\Projects;
use Gitlab\Client as GitlabClient;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Service\PipelineAndJobProvider;


class PipelineAndJobProviderTest extends WebTestCase
{
    /** @var PipelineAndJobProvider */
    protected $service;

    /** @var Client */
    protected $client;

    /** @var string */
    protected $projectId;

    /** @var string */
    protected $pipelineId;

    /** @var string */
    protected $jobId;

    protected $container;

    /** @var EntityManager */
    protected $em;

    protected static $fixtures;

    /** @throws \Exception */
    public function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->em = $this->container->get('doctrine.orm.entity_manager');

        /** @var PurgerLoader $loader */
        $loader = $this->container->get('fidry_alice_data_fixtures.loader.doctrine');
        $rootDir = $this->container->getParameter('kernel.project_dir');
        $fixturesPath = $rootDir . '/src/AppBundle/Resources/fixtures';

        self::$fixtures = $loader->load([
            $fixturesPath . '/001_groups.yml',
            $fixturesPath . '/002_project.yml',
            $fixturesPath . '/004_pipeline.yml',
            $fixturesPath . '/005_job.yml',
        ]);

        $this->projectId = 5;
        $project = $this->em->getRepository(Project::class)->findOneByProjectId($this->projectId);
        if (null == $project) {
            $project = new Project();
            $project->setProjectId($this->projectId)
                ->setName('test project');
            $this->em->persist($project);
            $this->em->flush();
        }

        $this->jobId = 76;
        $this->pipelineId = 9;
        $mockedProjects = $this->getMockBuilder(Projects::class)
            ->disableOriginalConstructor()
            ->setMethods(['pipeline'])
            ->getMock();
        $mockedProjects->method('pipeline')
            ->will($this->returnCallback(function ($projectId, $pipelineId) {
                if ($this->projectId !== $projectId || $this->pipelineId !== $pipelineId) {
                    throw new \Exception('Not Found');
                }
                //returns true instead of pipeline object as pipeline is not needed, just for check purpose
                return true;
            }));

        $mockedGitlabClient = $this->getMockBuilder(GitlabClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['projects'])
            ->getMock();
        $mockedGitlabClient->method('projects')
            ->willReturn($mockedProjects);

        $this->client->getContainer()->set('zeichen32_gitlabapi.client.gitlab', $mockedGitlabClient);
    }

    /** @throws \Exception */
    public function testWebhookRequestWithPipelineHeader()
    {
        $this->client->request(
            'POST',
            '/webhooks',
            [],
            [],
            [
                'HTTP_X-Gitlab-Event' => 'Pipeline Hook',
                'CONTENT_TYPE' => 'application/json'
            ],
            $this->generatePipelineTriggerContent($this->projectId, $this->pipelineId)
        );
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('', $this->client->getResponse()->getContent());
        $loadedPipelineFromDatabase = $this->em->getRepository(Pipeline::class)->findOneByPipelineId($this->pipelineId);
        $loadedPipelineId = $loadedPipelineFromDatabase->getPipelineId();
        $this->assertEquals($this->pipelineId, $loadedPipelineId);
    }

    /** @throws \Exception */
    public function testWebhookRequestWithJobHeader()
    {
        $this->client->request(
            'POST',
            '/webhooks',
            [],
            [],
            [
                'HTTP_X-Gitlab-Event' => 'Build Hook',
                'CONTENT_TYPE' => 'application/json'
            ],
            $this->generateJobTriggerContent($this->projectId, $this->jobId)

        );
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('', $this->client->getResponse()->getContent());
        $loadedJobFromDatabase = $this->em->getRepository(Job::class)->findOneByJobId($this->jobId);
        $loadedJobId = $loadedJobFromDatabase->getJobId();
        $this->assertEquals($this->jobId, $loadedJobId);
    }

    protected function generateJobTriggerContent($projectId, $jobId)
    {
        return \json_encode([
            'object_kind' => 'build',
            'project_id' => $projectId,
            'build_stage' => 'test',
            'build_status' => 'created',
            'build_id' => $jobId
        ]);
    }


    protected function generatePipelineTriggerContent($projectId, $pipelineId)
    {
        return \json_encode([
            'object_kind' => 'pipeline',
            'object_attributes' => [
                'id' => $pipelineId,
                'status' => 'success',
                'duration' => 45
            ],
            'project' => [
                'id' => $projectId
            ],
            'builds' => [
                [
                    'id' => $this->jobId,
                    'stage' => 'deploy',
                    'status' => 'success'
                ],
                [
                    'id' => 207,
                    'stage' => 'deploy',
                    'status' => 'created'
                ]
            ]
        ]);
    }
}