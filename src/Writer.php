<?php
namespace LoggingTap;

use Illuminate\Log\Writer as BaseWriter;

class Writer extends BaseWriter
{
    /**
     * Dynamically proxy method calls to the underlying logger.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->monolog->{$method}($parameters);
    }
}
