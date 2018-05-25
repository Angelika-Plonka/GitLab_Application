<?php

namespace AppBundle\Controller;

use AppBundle\Service\ProjectProvider;
use AppBundle\Service\WebhookProvider;
use AppBundle\Service\PipelineAndJobProvider;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;


class ProjectController extends Controller
{

    /**
     * @Route("/admin", name="admin");
     */
    public function adminAction(ProjectProvider $projectProvider)
    {
        return $this->render('admin.html.twig', array(
            'projects' => $projectProvider->findAll()
        ));
    }


    /**
     * @Route("/admin/projects", name="projects");
     * @throws \Exception
     */
    public function projectsAction(Request $request, ProjectProvider $projectProvider, WebhookProvider $webhookProvider)
    {

        if ($request->getMethod() === 'POST' && $request->get('addProjectID') !== "") {
            $projectID = $request->get('addProjectID');
            $webhookProvider->addToProject($projectID);
            $this->addFlash(
                'notice',
                'A webhook was added to project!'
            );
            return $this->redirectToRoute('projects');
        } elseif ($request->get('_method') === "DELETE" && $request->get('removeProjectID') !== "") {
            $projectID = $request->get('removeProjectID');
            $webhookProvider->removeFromProject($projectID);
            return $this->redirectToRoute('projects');
        }

        return $this->render('projects.html.twig', array(
            'projects' => $projectProvider->findAll()
        ));
    }


    /**
     * @Route("/api/pipelines", name="pipelines");
     * @Method("GET")
     */
    public function pipelinesAction(PipelineAndJobProvider $pipelineAndJobProvider, SerializerInterface $serializer)
    {
        $jobsWithPipeline = $pipelineAndJobProvider->findPipelinesWithJobs();
        return new Response($serializer->serialize($jobsWithPipeline, 'json', SerializationContext::create()->setGroups(array('pipelinesWithJobs'))));
    }

}

