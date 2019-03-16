<?php
namespace LoggingTap\Tests;

use Mockery;
use LoggingTap\Writer;
use Monolog\Logger as Monolog;
use Orchestra\Testbench\TestCase;

class WriterTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @test
     * @covers \LoggingTap\Writer::__call
     */
    public function it_dispatches_log_proxy_calls_to_underlying_logger()
    {
        $monolog = new Monolog($this->app->environment());
        $monolog = Mockery::mock($monolog)->makePartial();

        $monolog->shouldReceive('getHandlers')->once();

        $logger = new Writer(
            $monolog
        );

        $logger->getHandlers();
    }
}
