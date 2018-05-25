<?php

namespace Tests\Unit\AppBundle\Entity;

use AppBundle\Entity\Webhook;
use AppBundle\Entity\Project;
use PHPUnit\Framework\TestCase;

class WebhookTest extends TestCase
{
    public function testSettersAndGetters()
    {
        $entity = new Webhook();
        $this->assertEmpty($entity->getWebhookId());
        $this->assertEmpty($entity->getUrl());
        $this->assertEmpty($entity->getProject());

        $entity->setWebhookId(80);
        $this->assertEquals(80, $entity->getWebhookId());

        $entity->setUrl('https://blabla');
        $this->assertEquals('https://blabla', $entity->getUrl());

        $entity->setProject(new Project());
        $this->assertInstanceOf(Project::class, $entity->getProject());
    }
}