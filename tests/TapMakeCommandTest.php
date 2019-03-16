<?php
namespace LoggingTap\Tests;

use Mockery;
use LoggingTap\LogServiceProvider;
use Orchestra\Testbench\TestCase;
use Illuminate\Filesystem\Filesystem;
use LoggingTap\Console\TapMakeCommand;

class TapMakeCommandTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    protected function getPackageProviders($app)
    {
        return [LogServiceProvider::class];
    }

    /**
     * @test
     */
    public function it_creates_a_new_tap()
    {
        $files = Mockery::mock(Filesystem::class)->makePartial();

        $files->shouldReceive('exists')
            ->once()
            ->andReturn(false);

        $files->shouldReceive('isDirectory')->once()->andReturn(true);
        $files->shouldReceive('put')->once();

        $command = Mockery::mock(new TapMakeCommand($files));

        $this->app->instance('command.tap.make', $command);

        $this->artisan('make:tap', ['name' => 'foo']);
    }
}
