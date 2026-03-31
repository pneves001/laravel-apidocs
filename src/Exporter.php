<?php

namespace Pneves001\Apidocs;

class Exporter implements Export
{
    /**
     * Export Apidocs data into an array
     *
     * @param     Pneves001\Apidocs\Apidocs    $apidocs    apidocs
     * @return    array                                     apidocs data
     */
    public function export(Apidocs $apidocs): array
    {
        $name = $apidocs->getName();
        $stackConfig = config("apidocs.stacks.{$name}", []);
        
        $info = array_merge(config('apidocs.info', []), $stackConfig['info'] ?? []);
        $logo = $stackConfig['logo'] ?? config('apidocs.logo');
        $domain = $stackConfig['domain'] ?? config('apidocs.domain');

        $data = collect($apidocs->getRoutes())->map(function($item){
            return $item->data();
        })->all();

        $webhooks = collect($apidocs->getWebhooks())->map(function($item){
            return $item->data();
        })->all();

        return [
            'info' => $info,
            'logo' => $logo,
            'domain' => $domain,
            'groups' => $apidocs->groups(),
            'endpoints' => $data,
            'webhooks' => $webhooks,
        ];
    }
}
