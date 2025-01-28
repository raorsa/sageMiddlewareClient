<?php

namespace Raorsa\SageMiddlewareClient;

use Raorsa\SageMiddlewareClient\components\baseClient;

abstract class DeliveryNotes extends baseClient
{
    abstract protected function basePath(): string;

    public function list(string $team, bool $cache = true): object|false
    {
        return $this->callJson($this->basePath() . '/list/' . $team, $cache);
    }

    public function info(string $id, bool $cache = true): object|false
    {
        return $this->callJson($this->basePath() . '/info/' . $id, $cache);
    }

    public function lines(string $id, bool $cache = true): array|false
    {
        return $this->callJson($this->basePath() . '/lines/' . $id, $cache);
    }

    public function linesSN(string $sn, bool $cache = true): array|false
    {
        return $this->callJson($this->basePath() . '/lines-sn/' . $sn, $cache);
    }

    public function emptyLines(string $year, bool $cache = true): array|false
    {
        return $this->callJson($this->basePath() . '/empty-lines/' . $year, $cache);
    }

    public function find(string $query, bool $cache = true): array|false
    {
        return $this->callJson($this->basePath() . '/find/' . $query, $cache);
    }

    public function download(string $id, bool $cache = true): string|false
    {
        return $this->call($this->basePath() . '/download/' . $id, $cache);
    }

    public function img(string $id, bool $cache = true): string|false
    {
        return $this->call($this->basePath() . '/img/' . $id, $cache);
    }
}