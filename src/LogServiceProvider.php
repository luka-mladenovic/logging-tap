<?php
namespace LoggingTap;

use Illuminate\Support\Str;
use Monolog\Logger as Monolog;
use LoggingTap\Console\TapMakeCommand;
use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'TapMake' => 'command.tap.make',
    ];

    /**
     * Register the logger instance in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLogger();

        $this->registerCommands();
    }

    /**
     * Register the logger instance in the container.
     *
     * @return Tap\Writer
     */
    protected function registerLogger()
    {
        $this->app->instance('log', $logger = new Writer(
            new Monolog($this->app->environment()),
            $this->app['events']
        ));

        if ($this->app->hasMonologConfigurator()) {
            call_user_func(
                $this->app->getMonologConfigurator(),
                $logger->getMonolog()
            );
        } else {
            $this->configureHandlers($logger);
        }

        return $this->tap($logger);
    }

    /**
     * Apply the configured taps for the logger.
     *
     * @param Tap\Writer $logger
     *
     * @return void
     */
    protected function tap(Writer $logger)
    {
        foreach ($this->app->make('config')->get('app.log_tap', []) as $tap) {
            list($class, $arguments) = $this->parseTap($tap);

            $this->app->make($class)->__invoke($logger, explode(',', $arguments));
        }

        return $logger;
    }

    /**
     * Parse the given tap class string into a class name and arguments string.
     *
     * @param string $tap
     *
     * @return array
     */
    protected function parseTap($tap)
    {
        return Str::contains($tap, ':') ? explode(':', $tap, 2) : [$tap, ''];
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param \Illuminate\Contracts\Foundation\Application $this->app
     * @param \Illuminate\Log\Writer                       $logger
     *
     * @return void
     */
    protected function configureHandlers(Writer $logger)
    {
        $method = 'configure' . ucfirst($this->app['config']['app.log']) . 'Handler';

        $this->{$method}($logger);
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param \Illuminate\Log\Writer $logger
     *
     * @return void
     */
    protected function configureSingleHandler(Writer $logger)
    {
        $logger->useFiles($this->app->storagePath() . '/logs/laravel.log');
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param \Illuminate\Log\Writer $logger
     *
     * @return void
     */
    protected function configureDailyHandler(Writer $logger)
    {
        $logger->useDailyFiles(
            $this->app->storagePath() . '/logs/laravel.log',
            $this->app->make('config')->get('app.log_max_files', 5)
        );
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param \Illuminate\Log\Writer $logger
     *
     * @return void
     */
    protected function configureSyslogHandler(Writer $logger)
    {
        $logger->useSyslog('laravel');
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param \Illuminate\Log\Writer $logger
     *
     * @return void
     */
    protected function configureErrorlogHandler(Writer $logger)
    {
        $logger->useErrorLog();
    }

    /**
     * Register commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        foreach (array_keys($this->commands) as $command) {
            $method = "register{$command}Command";

            call_user_func_array([$this, $method], []);
        }

        $this->commands(array_values($this->commands));
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerTapMakeCommand()
    {
        $this->app->singleton('command.tap.make', function ($app) {
            return new TapMakeCommand($app['files']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array_values($this->commands);
    }
}
