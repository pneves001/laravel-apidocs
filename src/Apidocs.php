<?php

namespace Pneves001\Apidocs;

use Pneves001\Apidocs\Facades\Exporter;
use Pneves001\Apidocs\MarkdownExporter;
use Pneves001\Apidocs\Endpoints\Endpoint;
use Pneves001\Apidocs\Endpoints\Webhook;
use Pneves001\Apidocs\Exceptions\InvalidEndpoint;
use Route;

class Apidocs
{
    /**
     * stack name
     * @var    string
     */
    protected $name = 'default';

    /**
     * apidocs stacks
     * @var    array
     */
    protected static $stacks = [];

    /**
     * registered endpoints
     * @var    array
     */
    protected $routes = [];

    /**
     * registered webhooks
     * @var    array
     */
    protected $webhooks = [];

    /**
     * Get apidocs stack by its name
     *
     * @param     string    $name    stack name
     * @return    Apidocs
     */
    public static function stack(string $name = 'default'): Apidocs
    {
        if($name == 'default' && !isset(static::$stacks['default']))
        {
            $instance = app(\Pneves001\Apidocs\Apidocs::class);
            $instance->name = 'default';
            static::$stacks['default'] = $instance;
        }

        if(!isset(static::$stacks[$name]))
        {
            $instance = new static;
            $instance->name = $name;
            static::$stacks[$name] = $instance;
            
            // Define default group for the new stack
            $instance->defineGroup('non-groupped', 'Non-groupped', 'Non-grouped endpoints');
        }

        return static::$stacks[$name];
    }

    /**
     * Get stack name
     *
     * @return    string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get all stacks
     *
     * @return    array
     */
    public static function getStacks(): array
    {
        foreach (config('apidocs.stacks', []) as $name => $stack) {
            static::stack($name);
        }

        if(!isset(static::$stacks['default']))
            static::stack('default');

        return static::$stacks;
    }

    /**
     * registered groups
     * @var    array
     */
    protected $groups = [];

    /**
     * defered endpoint definitions
     * @var    array
     */
    protected $defered = [];

    /**
     * Register endpoint
     *
     * @param     mixed    $data    endpoint
     * @return    Pneves001\Apidocs\Endpoints\Endpoint endpoint
     */
    public function register($data): Endpoint
    {
        $endpoint = static::buildEndpoint($data);

        $this->routes[] = $endpoint;

        return $endpoint;
    }

    /**
     * Register webhook
     *
     * @param     mixed    $data    webhook
     * @return    Pneves001\Apidocs\Endpoints\Webhook webhook
     */
    public function registerWebhook($data): Webhook
    {
        $webhook = static::buildWebhook($data);

        $this->webhooks[] = $webhook;

        return $webhook;
    }

    /**
     * Register endpoint
     *
     * @param     mixed    $data    endpoint
     * @param     mixed    $route   route
     * @return    Pneves001\Apidocs\Endpoints\Endpoint endpoint
     */
    public function registerRoute($data, $route): Endpoint
    {
        return $this->register($data)
            ->method($route->methods()[0])
            ->uri($route->uri())
            ->group('non-groupped')
            ->mount();
    }

    /**
     * Build endpoint using provided data
     *
     * @param     mixed      $data    endpoint data
     * @return    Pneves001\Apidocs\Endpoints\Endpoint endpoint
     * @throws    Pneves001\Apidocs\Exceptions\InvalidEndpoint
     */
    protected function buildEndpoint($data): Endpoint
    {
        if(!$data)
            return app(Endpoint::class);

        // ONLY resolve from the app container if it's explicitly intended to be an Endpoint
        if(is_string($data) && class_exists($data) && is_subclass_of($data, Endpoint::class)) {
            return app($data);
        }

        if($data instanceof Endpoint)
            return $data;

        throw new InvalidEndpoint("The provided data must be an instance of Endpoint or a class extending it.");
    }
    /**
     * Build webhook using provided data
     *
     * @param     mixed      $data    webhook data
     * @return    Pneves001\Apidocs\Endpoints\Webhook webhook
     * @throws    Pneves001\Apidocs\Exceptions\InvalidEndpoint
     */
    protected function buildWebhook($data): Webhook
    {
        if(!$data)
            return app(Webhook::class);

        if(is_string($data) && class_exists($data))
            return app($data);

        if($data instanceof Webhook)
            return $data;

        throw new InvalidEndpoint;
    }

    /**
     * Create endpoint definitions for all of the routes
     *
     */
    protected function compile()
    {
        if($this->getDefered())
        {
            $this->describeDefered();
        }

        foreach ($this->routes as $route) {
            if (!$route->get('id')) {
                $route->mount();
            }
        }

        foreach ($this->webhooks as $webhook) {
            if (!$webhook->get('id')) {
                $webhook->mount();
            }
        }
    }

    /**
     * Create endpoint definitions for defered routes
     *
     */
    protected function describeDefered()
    {
        $routes = Route::getRoutes();

        foreach($this->defered as $name => $definition)
        {
            if($route = $routes->getByName($name))
                $this->registerRoute($definition, $route);
        }

        $this->defered = [];
    }

    /**
     * Exports apidocs data
     *
     * @return    array    array of data
     */
    public function export(): array
    {
        $this->compile();

        return Exporter::export($this);
    }

    /**
     * Exports apidocs data as markdown
     *
     * @return    string
     */
    public function exportMarkdown(): string
    {
        $this->compile();

        return (new MarkdownExporter)->export($this);
    }

    /**
     * Returns all registered endpoints
     *
     * @return    array    registered endpoints
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Returns all registered webhooks
     *
     * @return    array    registered webhooks
     */
    public function getWebhooks(): array
    {
        return $this->webhooks;
    }

    /**
     * Define group of endpoints
     *
     * @param     string    $slug           group friendly name
     * @param     string    $name           group name
     * @param     string    $description    group description
     */
    public function defineGroup(string $slug, string $name, ?string $description = NULL)
    {
        $this->groups[$slug] = [
            'name' => $name,
            'description' => $description,
        ];
    }

    /**
     * Returns all defned groups
     *
     * @return    array    groups
     */
    public function groups(): array
    {
        return $this->groups;
    }

    /**
     * Check if group has been defined
     *
     * @param     string    $slug    group friendly name
     * @return    bool
     */
    public function groupDefined(string $slug): bool
    {
        return isset($this->groups[$slug]);
    }

    /**
     * Returns group by its friendly name
     *
     * @param     string    $slug    group friendly name
     * @return    array              group data
     */
    public function getGroup(string $slug): array
    {
        return $this->groups[$slug];
    }

    /**
     * Defer endpoint definition
     *
     * @param     array     $definitions
     */
    public function defer(array $definitions)
    {
        $this->defered = array_merge($this->defered, $definitions);
    }

    /**
     * Return defered definitions
     *
     * @return    array    defered definitions
     */
    public function getDefered(): array
    {
        return $this->defered;
    }
}
