<?php

use Pneves001\Apidocs\Facades\Apidocs;

/**
 * Define defered Apidocs definitions
 *
 * @param     array     $definitions
 * @param     string    $stack
 */
function apidocs(array $definitions, string $stack = 'default')
{
    \Pneves001\Apidocs\Apidocs::stack($stack)->defer($definitions);
}

/**
 * Register Apidocs Webhook
 *
 * @param     mixed     $data
 * @param     string    $stack
 */
function apidocs_webhook($data, string $stack = 'default')
{
    return \Pneves001\Apidocs\Apidocs::stack($stack)->registerWebhook($data);
}
