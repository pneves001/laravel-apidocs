<?php

namespace Pneves001\Apidocs\Endpoints;

class Webhook extends Endpoint
{
    /**
     * Describe webhook
     *
     */
    public function describe(): void
    {
        //
    }

    /**
     * Mounts webhook within Apidocs container
     *
     * @return    Pneves001\Apidocs\Endpoints\Webhook mutated webhook
     */
    public function mount(): Endpoint
    {
        parent::mount();

        $this->set('is_webhook', true);

        return $this;
    }
}
