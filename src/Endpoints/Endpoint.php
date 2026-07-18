<?php

namespace Pneves001\Apidocs\Endpoints;

use Pneves001\Apidocs\Facades\Apidocs;
use Pneves001\Apidocs\Traits\KeepsData;
use Pneves001\Apidocs\Facades\Explain;
use Pneves001\Apidocs\Params\Param;
use Pneves001\Apidocs\Exceptions\InvalidParamValue;
use Pneves001\Apidocs\Exceptions\GroupNotFound;
use Illuminate\Support\Str;
use Error;

use Log; 

class Endpoint
{
    use KeepsData;

    /**
     * Describe endpoint
     *
     */
    public function describe(): void
    {
        //
    }

    /**
     * Mounts endpoint within Apidocs container
     *
     * @return    Pneves001\Apidocs\Endpoints\Endpoint mutated endpoint
     */
    public function mount(): Endpoint
    {
        if ($this->get('id')) {
            return $this;
        }

        $this->describe();
        $this->set('id', "item-". Str::uuid());

        return $this;
    }

    /**
     * Sets endpoint method
     *
     * @param     string      $method    endpoint method
     * @return    Pneves001\Apidocs\Endpoints\Endpoint mutated endpoint
     */
    public function method(string $method): Endpoint
    {
        return $this->set('method', $method);
    }

    /**
     * Sets endpoint uri
     *
     * @param     string      $uri    endpoint uri
     * @return    Pneves001\Apidocs\Endpoints\Endpoint mutated endpoint
     */
    public function uri(string $uri): Endpoint
    {
        return $this->set('uri', $uri);
    }

    /**
    * Sets endpoint group
    *
    * @param     string      $slug    group slug
    * @return    Pneves001\Apidocs\Endpoints\Endpoint mutated endpoint
    * @throws    Pneves001\Apidocs\Exceptions\GroupNotFound
    */
    public function group(string $slug): Endpoint
    {
        if(!Apidocs::groupDefined($slug))
            throw new GroupNotFound("Group `$slug` not found. Please register it");

        return $this->set('group', $slug);
    }

    /**
     * Sets endpoint group
     *
     * @param     boolean     $deprecated
     * @return    Pneves001\Apidocs\Endpoints\Endpoint mutated endpoint
     */
    public function deprecated(bool $deprecated = TRUE): Endpoint
    {
        return $this->set('deprecated', $deprecated);
    }

    /**
     * Sets endpoint title
     *
     * @param     boolean     $title endoint title
     * @return    Pneves001\Apidocs\Endpoints\Endpoint mutated endpoint
     */
    public function title(string $title): Endpoint
    {
        return $this->set('title', $title);
    }

    /**
     * Sets endpoint description
     *
     * @param     string     $description endoint description
     * @return    Pneves001\Apidocs\Endpoints\Endpoint mutated endpoint
     */
    public function description(string $description): Endpoint
    {
        return $this->set('description', $description);
    }

    /**
     * Sets endpoint description
     * Alias for `description`
     *
     * @see `description`
     * @param     string     $description endoint description
     * @return    Pneves001\Apidocs\Endpoints\Endpoint mutated endpoint
     */
    public function desc(string $description): Endpoint
    {
        return $this->description(...func_get_args());
    }

    /**
     * Sets endpoint query params
     *
     * @param     boolean     $data  query params
     * @return    Pneves001\Apidocs\Endpoints\Endpoint mutated endpoint
     */
    public function query(array $data): Endpoint
    {
        return $this->set("query", $this->buildParams($data));
    }

    /**
     * Sets endpoint route params
     *
     * @param     boolean     $data  route params
     * @return    Pneves001\Apidocs\Endpoints\Endpoint mutated endpoint
     */
    public function params(array $data): Endpoint
    {
        return $this->set("params", $this->buildParams($data));
    }

    /**
     * Sets endpoint route params
     *
     * @param     array      $data  body params
     * @param     string     $format  body format
     * @return    Pneves001\Apidocs\Endpoints\Endpoint mutated endpoint
     */
    public function body(array $data, string $format = ''): Endpoint
    {
        return $this->set("body.format", $format)
                ->set("body.data", $this->buildParams($data));
    }

    /**
     * Sets endpoint header
     *
     * @param     string     $key    header name
     * @param     string     $value  header value
     * @return    Pneves001\Apidocs\Endpoints\Endpoint mutated endpoint
     */
    public function header(string $key, string $value): Endpoint
    {
        return $this->headers([$key => $value]);
    }

    /**
     * Sets endpoint headers
     *
     * @param     array     $data    endpoint headers
     * @return    Pneves001\Apidocs\Endpoints\Endpoint mutated endpoint
     */
    public function headers(array $data): Endpoint
    {
        $headers = array_merge($this->get('headers', []), $data);

        return $this->set('headers', $headers);
    }

    /**
     * Sets endpoint example
     *
     * @param     mixed      $data     endpoint example
     * @param     string     $title    optional endpoint title
     * @return    Pneves001\Apidocs\Endpoints\Endpoint mutated endpoint
     */
    public function example($data, string $title = NULL): Endpoint
    {
        return $this->set('examples', [
            'title' => $title,
            'data' => $this->normalizeData($data),
        ], TRUE);
    }

    /**
     * Sets endpoint examples
     *
     * @param     array      $data     endpoint examples
     * @return    Pneves001\Apidocs\Endpoints\Endpoint mutated endpoint
     */
    public function examples(array $data): Endpoint
    {
        foreach($data as $example)
            $this->example($example);

        return $this;
    }

    /**
     * Sets endpoint expected response
     *
     * @param     string     $code     response status code
     * @param     mixed      $response  endpoint response
     * @param     string     $description     optional response description
     * @return    Pneves001\Apidocs\Endpoints\Endpoint mutated endpoint
     */
        public function returns(string $label, $response, string $description = ''): Endpoint
        {
            // Ensure we are passing the data correctly to the 'set' method
            $data = [
                'response' => $this->normalizeData($response),
                'description' => $description,
            ];

            // 'returns.' + 'success' or 'error' will create an array path like ['returns' => ['success' => ...]]
            return $this->set("returns.$label", $data, FALSE);
        }

    /**
     * Normalize data for export
     *
     * @param     mixed    $data
     * @return    array
     */
    protected function normalizeData($data): array
    {
        Log::info($data); 

        // Add this to the top of normalizeData
        if (isset($data['type']) && $data['type'] === 'class') {
             return $data; // Keep the reflection metadata intact
        }

        if (is_string($data)) {
            $json = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $json;
            }
        }

        if (is_object($data)) {
            if (method_exists($data, 'toArray')) {
                return $data->toArray();
            }

            if ($data instanceof \JsonSerializable) {
                return $data->jsonSerialize();
            }
        }

        return (array)$data;
    }

    /**
     * Build parameters from given set
     *
     * @param     array    $params    parameters set
     * @return    array               properly built parameters array
     */
    protected function buildParams(array $params): array
    {
        return collect($params)->mapWithKeys(function($value, $key){
            $var = $this->resolveValue($value);
            $name = $this->guessVariableName($key, $var);

            return [
                $name => $var
            ];

        })->all();
    }

    /**
     * Resolve parameter values
     *
     * @param     mixed    $value
     * @return    array              param values
     * @throws    Pneves001\Apidocs\Exceptions\InvalidParamValue
     */
    protected function resolveValue($value): array
        {
            if (is_string($value) && class_exists($value)) {
                try {
                    // Instead of making the class (which crashes on bad constructors),
                    // just reflect on it to see if it exists.
                    $reflection = new \ReflectionClass($value);
                    
                    // If you get here, the class exists and is valid. 
                    // Return a dummy representation instead of the full object.
                    return ['type' => 'class', 'name' => $reflection->getName()];
                    
                } catch (\Throwable $e) {
                    \Log::error("Apidocs failed to inspect class [$value]: " . $e->getMessage());
                    return ['error' => 'Incompatible class structure'];
                }
            }
            // ...
        }
    /**
     * Guess variable name
     *
     * this method tries to guess real parameter name using
     * parameter `name` property. If not foud, parameter key will be returned
     *
     * @param     mixed     $key      parameter key
     * @param     array     $value    parameter values
     * @return    string              guessed variable name
     */
    protected function guessVariableName($key, array $value): string
    {
        if(is_numeric($key) && isset($value['name']))
            return $value['name'];

        return $key;
    }

    public function __call($name, $args)
    {
	if (!empty($name)) {
   		 return $this->returns($name, ...$args);
        }

    throw new Error('Call to undefined method '.__CLASS__.'::'.$name.'()');
    }
}
