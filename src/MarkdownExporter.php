<?php

namespace Pneves001\Apidocs;

class MarkdownExporter
{
    public function export(Apidocs $apidocs): string
    {
        $name = $apidocs->getName();
        $stackConfig = config("apidocs.stacks.{$name}", []);
        
        $info = array_merge(config('apidocs.info', []), $stackConfig['info'] ?? []);
        $domain = $stackConfig['domain'] ?? config('apidocs.domain');

        $groups = $apidocs->groups();
        $endpoints = collect($apidocs->getRoutes())->map(fn($e) => $e->data());
        $webhooks = collect($apidocs->getWebhooks())->map(fn($e) => $e->data());

        $md = "# {$info['title']} (v{$info['version']})\n\n";
        $md .= "{$info['description']}\n\n";
        $md .= "Domain: " . $domain . "\n\n";

        foreach ($groups as $slug => $group) {
            $md .= "## {$group['name']}\n\n";
            if ($group['description']) {
                $md .= "{$group['description']}\n\n";
            }

            $groupEndpoints = $endpoints->where('group', $slug);
            $groupWebhooks = $webhooks->where('group', $slug);

            if ($groupEndpoints->isNotEmpty()) {
                foreach ($groupEndpoints as $endpoint) {
                    $md .= $this->formatResource($endpoint);
                }
            }

            if ($groupWebhooks->isNotEmpty()) {
                $md .= "### Webhooks\n\n";
                foreach ($groupWebhooks as $webhook) {
                    $md .= $this->formatResource($webhook);
                }
            }
        }

        return $md;
    }

    protected function formatResource(array $endpoint): string
    {
        file_put_contents(storage_path('logs/exporter_debug.log'), json_encode($endpoint) . PHP_EOL, FILE_APPEND);

        $md = "### {$endpoint['title']}\n\n";
        if ($endpoint['description'] ?? null) {
            $md .= "{$endpoint['description']}\n\n";
        }
        $md .= "**Method:** `{$endpoint['method']}`\n\n";
        $md .= "**URI:** `{$endpoint['uri']}`\n\n";

        if ($params = $endpoint['params'] ?? null) {
            $md .= "#### Route Parameters\n\n";
            $md .= $this->formatParams($params);
        }

        if ($query = $endpoint['query'] ?? null) {
            $md .= "#### Query Parameters\n\n";
            $md .= $this->formatParams($query);
        }

        if ($body = $endpoint['body']['data'] ?? null) {
            $md .= "#### Body Parameters (" . ($endpoint['body']['format'] ?: 'JSON') . ")\n\n";
            $md .= $this->formatParams($body);
        }

        if ($headers = $endpoint['headers'] ?? null) {
            $md .= "#### Headers\n\n";
            foreach ($headers as $key => $value) {
                $md .= "- `{$key}`: `{$value}`\n";
            }
            $md .= "\n";
        }

        if ($examples = $endpoint['examples'] ?? null) {
            $md .= "#### Examples\n\n";
            foreach ($examples as $example) {
                if ($example['title']) {
                    $md .= "_{$example['title']}_\n";
                }
                $md .= "```json\n" . json_encode($example['data'], JSON_PRETTY_PRINT) . "\n```\n\n";
            }
        }

        if ($returns = $endpoint['returns'] ?? null) 
                {
                $md .= "#### Responses\n\n";
                
                foreach ($returns as $label => $entry) {
                            $md .= "##### " . ucfirst($label) . " Response\n";
                            
                            // entry contains ['response' => [...], 'description' => '...']
                            $description = $entry['description'] ?? '';
                            $responseBody = $entry['response'] ?? null;

                            if (!empty($description)) {
                                $md .= "_{$description}_\n\n";
                            }
                            
                            // If responseBody is still empty, the normalization failed in the Endpoint class
                            if ($responseBody !== null) {
                                $md .= "```json\n" . json_encode($responseBody, JSON_PRETTY_PRINT) . "\n```\n\n";
                            } else {
                                $md .= "_Error: Response body was empty. Check Endpoint normalization._\n\n";
                            }
                        }
                }

            $md .= "---\n\n";

            return $md;
            }

    protected function formatParams(array $params): string
    {
        $md = "| Name | Type | Required | Description | Example |\n";
        $md .= "| --- | --- | --- | --- | --- |\n";
        foreach ($params as $name => $p) {
            $type = $p['type'] ?? 'string';
            $required = ($p['required'] ?? false) ? 'Yes' : 'No';
            $description = $p['description'] ?? '-';
            $example = isset($p['examples']) ? json_encode($p['examples']) : '-';
            $md .= "| `{$name}` | `{$type}` | {$required} | {$description} | `{$examples}` |\n";
        }
        $md .= "\n";
        return $md;
    }
}
