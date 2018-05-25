<?php

namespace AppBundle\Command;

use Gitlab\Client;
use Gitlab\ResultPager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;
use AppBundle\Service\LoadedGitLabApi;
use AppBundle\Service\ProjectProvider;
use AppBundle\Service\GroupProvider;
use Psr\Log\LoggerInterface;


/**
 * Display a GitLab projects
 */
class LoadProjectCommand extends Command
{
    /** @var Client */
    protected $gitlabClient;

    /** @var ProjectProvider */
    protected $projectProvider;

    /** @var GroupProvider */
    protected $groupProvider;

    /** @var ResultPager */
    protected $gitlabAPIPager;

    /** @var LoadedGitLabApi */
    protected $loadedGitLabApi;

    /** @var string */
    protected $environment;

    /** @var LoggerInterface */
    protected $logger;

    public function __construct(Client $gitlabClient, ProjectProvider $projectProvider, GroupProvider $groupProvider, LoadedGitLabApi $loadedGitLabApi, string $environment, LoggerInterface $logger)
    {
        $this->gitlabClient = $gitlabClient;
        $this->projectProvider = $projectProvider;
        $this->groupProvider = $groupProvider;
        $this->loadedGitLabApi = $loadedGitLabApi;
        $this->gitlabAPIPager = new ResultPager($this->gitlabClient);
        $this->environment = $environment;
        $this->logger = $logger;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('gitlab:projects');
    }

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $groups = $this->gitlabAPIPager->fetch($this->gitlabClient->api('namespaces'), 'all');
            do {
                $groupMap = $this->loadedGitLabApi->loadNamespaces($groups);
                $this->groupProvider->synchronize($groupMap);
                if ($this->environment === 'dev' || $this->environment === 'test') {
                    $output->writeln('GROUPS {' . json_encode($groupMap) . '}');
                }
                $this->logger->info('GROUPS {' . json_encode($groupMap) . '}');

            } while ($this->gitlabAPIPager->hasNext() && ($groups = $this->gitlabAPIPager->fetchNext()) !== null);


            $projects = $this->gitlabAPIPager->fetch($this->gitlabClient->api('projects'), 'all');
            do {
                $projectMap = $this->loadedGitLabApi->loadGitLabData($projects);
                $this->projectProvider->synchronize($projectMap);
                if ($this->environment === 'dev' || $this->environment === 'test') {
                    $output->writeln('PROJECTS {' . json_encode($projectMap) . '}');
                }
                $this->logger->info('PROJECTS {' . json_encode($projectMap) . '}');

            } while ($this->gitlabAPIPager->hasNext() && ($projects = $this->gitlabAPIPager->fetchNext()) !== null);

        } catch (Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
