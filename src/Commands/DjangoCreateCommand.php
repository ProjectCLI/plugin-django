<?php

/**
 * Plugin for ProjectCLI. More info at
 * https://github.com/chriha/project-cli
 */

namespace ProjectCLI\Django\Commands;

use Chriha\ProjectCLI\Commands\Command;
use Chriha\ProjectCLI\Helpers;
use Chriha\ProjectCLI\Services\Docker;
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
     * @param Docker $docker
     * @return void
     */
    public function handle(Docker $docker) : void
    {
        $name = $this->argument('name');

        $docker->exec('web', ['django-admin', 'startproject', $name])
            ->setTty(true)
            ->run(
                function ($type, $buffer)
                {
                    $this->output->write($buffer);
                }
            );

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
        return PROJECT_IS_INSIDE
            && (Helpers::isProjectType('python') || Helpers::isProjectType('django'))
            && ! file_exists(Helpers::projectPath('src/manage.py'));
    }

}
