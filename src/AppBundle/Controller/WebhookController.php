<?php

namespace AppBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\PipelineAndJobProvider;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Service\ClientProvider;

class WebhookController extends Controller
{

    /**
     * @Route("/webhooks");
     * @Method("POST")
     * @return Response
     */
    public function webhooksAction(Request $request, PipelineAndJobProvider $pipelineAndJobProvider, LoggerInterface $logger, ClientProvider $clientProvider): Response
    {
        try {
            $header = $request->headers->get('X-Gitlab-Event');
            if ($header == 'Pipeline Hook') {
                $pipeline = $pipelineAndJobProvider->handlePipeline(json_decode($request->getContent()));
                $logger->debug("Received Pipeline hook: {$pipeline->getPipelineId()}");
                $msg = "UPDATE: new pipeline: {$pipeline->getPipelineId()}";
                $clientProvider->createClient($msg);

            } elseif ($header == 'Build Hook') {
                $job = $pipelineAndJobProvider->handleJob(json_decode($request->getContent()));
                $logger->debug("Received Build hook: {$job->getJobId()}");
                $msg = "UPDATE: new job: {$job->getJobId()}";
                $clientProvider->createClient($msg);
            }

        } catch (\Exception $e) {
            $logger->error($e->getMessage());
            return new Response($e->getMessage(), 200);
        }

        return new Response(null, 200);
    }

}
