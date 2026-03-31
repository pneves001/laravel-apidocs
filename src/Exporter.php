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

        $groups = $apidocs->groups();

        // Data-Level Fix: Ensure all group slugs used by endpoints exist in the groups array.
        // This prevents the "Cannot read properties of undefined (reading 'name')" error 
        // in older, uncompiled versions of the frontend.
        foreach ($data as $endpoint) {
            $slug = $endpoint['group'] ?? 'non-groupped';
            if (!isset($groups[$slug])) {
                $groups[$slug] = [
                    'name' => ucfirst(str_replace(['-', '_'], ' ', $slug)),
                    'description' => ''
                ];
            }
        }
        
        foreach ($webhooks as $webhook) {
            $slug = $webhook['group'] ?? 'non-groupped';
            if (!isset($groups[$slug])) {
                $groups[$slug] = [
                    'name' => ucfirst(str_replace(['-', '_'], ' ', $slug)),
                    'description' => ''
                ];
            }
        }

        return [
            'info' => $info,
            'logo' => $logo,
            'domain' => $domain,
            'groups' => $groups,
            'endpoints' => $data,
            'webhooks' => $webhooks,
        ];
    }
}
