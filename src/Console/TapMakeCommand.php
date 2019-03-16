<?php
namespace LoggingTap\Console;

use Illuminate\Console\GeneratorCommand;

class TapMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:tap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new logging tap class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Tap';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/tap.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Logging';
    }
}
