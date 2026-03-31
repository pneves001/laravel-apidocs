<?php

namespace Pneves001\Apidocs\Console\Commands;

class MakeWebhook extends Make
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apidocs:webhook {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Apidocs Webhook';

    /**
     * @inheritdoc
     */
    protected function targetLocation(): string
    {
        return config('apidocs.dir.endpoints');
    }

    /**
     * @inheritdoc
     */
    protected function stub(): string
    {
        return __DIR__.'/../../../stubs/webhook.stub';
    }
}
