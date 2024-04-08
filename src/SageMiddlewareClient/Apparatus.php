<?php

namespace Raorsa\SageMiddlewareClient;

class Apparatus extends GenericClient
{
    const BASE = 'apparatus';

    public function list(string $client)
    {
        return $this->callJson(self::BASE . '/' . $client);
    }

    public function interventions(string $id)
    {
        return $this->callJson(self::BASE . '/' . $id . '/interventions');
    }

    public function integrated(string $id)
    {
        return $this->callJson(self::BASE . '/' . $id . '/integrated');
    }
    public function brands()
    {
        return $this->callJson(self::BASE . '/brands');
    }

    public function types()
    {
        return $this->callJson(self::BASE . '/types');
    }

    public function warranties()
    {
        return $this->callJson(self::BASE . '/warranties');
    }

    public function interventionsType()
    {
        return $this->callJson(self::BASE . '/interventions-type');
    }
}