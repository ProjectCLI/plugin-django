<?php

/**
 * Plugin for ProjectCLI. More info at
 * https://github.com/chriha/project-cli
 */

namespace ProjectCLI\Django\Commands;

use Chriha\ProjectCLI\Commands\Command;
use Chriha\ProjectCLI\Helpers;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class DjangoCreateCommand extends Command
{

    /** @var string */
    protected static $defaultName = 'django:create';

    /** @var string */
    protected $description = 'Create a new Django project.';


    /**
     * Configure the command by adding a description, arguments and options
     *
     * @return void
     */
    public function configure() : void
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'The name of the project');
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() : void
    {
        $name = $this->argument('name');

        $this->call('django:admin', ['startproject', $name]);

        $src = Helpers::projectPath('src');
        $tmp = Helpers::projectPath('temp/cp');

        mkdir($tmp, 0755, true);

        Helpers::recursiveCopy("{$src}/{$name}", $tmp);
        copy("{$src}/.gitignore", "{$tmp}/.gitignore");

        Helpers::rmdir($src);
        Helpers::recursiveCopy($tmp, $src);
        Helpers::rmdir($tmp);

        $this->call('restart');
        $this->info('Project successfully created');
    }

    /**
     * Make command only available if inside the project
     */
    public static function isActive() : bool
    {
        return PROJECT_IS_INSIDE && Helpers::isProjectType('django');
    }

}
