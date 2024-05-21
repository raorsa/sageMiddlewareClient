<?php

namespace Raorsa\SageMiddlewareClient;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class logWrapper implements LogWrapperInterface
{

    private $log_dir = null;
    private $lengthLogData = null;

    public function __construct(string $log_dir = null, int $lengthLogData = 100)
    {

        if (is_null($log_dir)) {
            $log_dir = (function_exists('storage_path') ? storage_path() . '/' : "") . "logs/";
        }
        $this->lengthLogData = $lengthLogData;

    }

    /**
     * @param false|string $data
     * @return void
     */
    private function log($data): void
    {
        if (!is_null($this->log_dir)) {
            $log = new Logger('Sageclient');
            $log->pushHandler(new StreamHandler($this->log_dir . '/raorsa.log', Level::fromName($_ENV['LOG_LEVEL'])));
            if ($this->lengthLogData !== 0) {
                $data = substr($data, 0, $this->lengthLogData);
            }
            $log->info($data);
        }
    }

    public function logCache($path, $data, $verb)
    {
        $this->log(substr(str_pad('CACHE ' . $verb, 15, ' '), 0, 15) . '||' . $path . '->' . $data);
    }

    public function logServer($path, $data, $verb)
    {
        $this->log(substr(str_pad('SERVER ' . $verb, 15, ' '), 0, 15) . '||' . $path . '->' . $data);
    }
}