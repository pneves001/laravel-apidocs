<?php

namespace Pneves001\Apidocs\Tests;

use Pneves001\Apidocs\Facades\Apidocs;
use Pneves001\Apidocs\Endpoints\Webhook;
use Pneves001\Apidocs\Exceptions\InvalidEndpoint;

class WebhookTest extends TestCase
{
    /** @test */
    public function can_register_webhook()
    {
        $webhook = Apidocs::registerWebhook(NULL);
        $this->assertTrue($webhook instanceof Webhook);
        $this->assertCount(1, Apidocs::getWebhooks());

        $webhook = apidocs_webhook(NULL);
        $this->assertTrue($webhook instanceof Webhook);
        $this->assertCount(2, Apidocs::getWebhooks());
    }

    /** @test */
    public function webhook_has_is_webhook_flag()
    {
        $webhook = Apidocs::registerWebhook(NULL)->mount();
        $this->assertTrue($webhook->get('is_webhook'));
    }

    /** @test */
    public function export_includes_webhooks()
    {
        Apidocs::registerWebhook(NULL)->title('My Webhook')->mount();
        
        $exported = Apidocs::export();
        
        $this->assertArrayHasKey('webhooks', $exported);
        $this->assertCount(1, $exported['webhooks']);
        $this->assertEquals('My Webhook', $exported['webhooks'][0]['title']);
        $this->assertTrue($exported['webhooks'][0]['is_webhook']);
    }
}
