<?php 

namespace Statix\FormAction\Concerns;

use Illuminate\Http\Request;

trait InteractsWithTheRequest
{
    public function setRequest(Request $request): static
    {
        $this->request = $request;

        return $this;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->request->get($key, $default);
    }

    public function set(string|array $key, mixed $value = null, bool $replace = false): static
    {
        if(is_array($key)) {
            if($replace) {
                $this->request->replace($key);
            } else {
                $this->request->merge($key);
            }

            return $this;
        }

        if(is_callable($value)) {
            $value = $this->app->call($value, ['action' => $this]);
        }

        if($replace) {
            $this->request->replace([$key => $value]);

            return $this;
        }

        if(! $this->request->has($key)) {
            $this->request->merge([$key => $value]);
        }

        return $this;
    }

    public function has(string|array $key): bool
    {
        return $this->request->has($key);
    }
}