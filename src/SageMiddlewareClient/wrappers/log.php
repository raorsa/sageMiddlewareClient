<?php

namespace Raorsa\SageMiddlewareClient\wrappers;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use function Raorsa\SageMiddlewareClient\storage_path;

class log
{

    private string $log_dir;
    private int $lengthLogData;

    public function __construct(string $log_dir = null, int $lengthLogData = 100)
    {

        if (is_null($log_dir)) {
            $this->log_dir = "logs/";
        }
        $this->lengthLogData = $lengthLogData;

    }

    /**
     * @param string $data
     * @return void
     */
    private function log(string $data): void
    {
        $log = new Logger('Sageclient');
        $log->pushHandler(new StreamHandler($this->log_dir . '/raorsa.log', Level::fromName($_ENV['LOG_LEVEL'])));
        if ($this->lengthLogData !== 0) {
            $data = substr($data, 0, $this->lengthLogData);
        }
        $log->info($data);
    }

    public function logCache(string $path, string $data, string $verb): void
    {
        $this->log(substr(str_pad('CACHE ' . $verb, 15), 0, 15) . '||' . $path . '->' . $data);
    }

    public function logServer(string $path, string $data, string $verb): void
    {
        $this->log(substr(str_pad('SERVER ' . $verb, 15), 0, 15) . '||' . $path . '->' . $data);
    }
}