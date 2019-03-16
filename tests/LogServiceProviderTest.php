<?php
namespace LoggingTap\Tests;

use Mockery;
use LoggingTap\Writer;
use LoggingTap\LogServiceProvider;
use Orchestra\Testbench\TestCase;
use LoggingTap\Console\TapMakeCommand;

class LogServiceProviderTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @test
     * @covers \LoggingTap\LogServiceProvider::register
     * @covers \LoggingTap\LogServiceProvider::registerLogger
     */
    public function it_registers_the_logger_instance()
    {
        $provider = new LogServiceProvider($this->app);
        $provider->register();

        $this->assertInstanceOf(
            Writer::class,
            $this->app->make('log')
        );
    }

    /**
     * @test
     * @covers \LoggingTap\LogServiceProvider::provides
     * @covers \LoggingTap\LogServiceProvider::registerCommands
     * @covers \LoggingTap\LogServiceProvider::registerTapMakeCommand
     */
    public function it_registers_the_tap_make_command()
    {
        $provider = new LogServiceProvider($this->app);
        $provider->register();

        $this->assertInstanceOf(
            TapMakeCommand::class,
            $this->app->make('command.tap.make')
        );

        $this->assertContains(
            'command.tap.make',
            $provider->provides()
        );
    }

    /**
     * @test
     * @covers \LoggingTap\LogServiceProvider::tap
     * @covers \LoggingTap\LogServiceProvider::parseTap
     */
    public function it_invokes_the_provided_tap()
    {
        $tap    = Mockery::mock(ExampleTap::class);
        $app    = Mockery::mock($this->app)->makePartial();
        $config = Mockery::mock($this->app->make('config'))->makePartial();

        $app->shouldReceive('make')
            ->with('config')
            ->andReturn($config);

        $config->shouldReceive('get')
            ->with('app.log_tap', [])
            ->andReturn([ExampleTap::class]);

        $tap->shouldReceive('__invoke')
            ->once();

        $app->shouldReceive('make')
            ->with(ExampleTap::class)
            ->andReturn($tap);

        $provider = new LogServiceProvider($app);
        $provider->register();
    }

    /**
     * @test
     * @dataProvider handlerDataProvider
     * @covers \LoggingTap\LogServiceProvider::configureHandlers
     * @covers \LoggingTap\LogServiceProvider::configureDailyHandler
     * @covers \LoggingTap\LogServiceProvider::configureSyslogHandler
     * @covers \LoggingTap\LogServiceProvider::configureSingleHandler
     * @covers \LoggingTap\LogServiceProvider::configureErrorlogHandler
     *
     * @param mixed $handler
     */
    public function it_configures_the_handles($handler)
    {
        $this->app['config']['app.log'] = $handler;

        $provider = new LogServiceProvider($this->app);
        $provider->register();
    }

    public function handlerDataProvider()
    {
        return [
            ['single'],
            ['daily'],
            ['syslog'],
            ['errorlog'],
        ];
    }
}

class ExampleTap
{
    public function __invoke($logger, $arguments)
    {
    }
}
