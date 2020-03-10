<?php

/**
 * Plugin for ProjectCLI. More info at
 * https://github.com/chriha/project-cli
 */

namespace ProjectCLI\Django\Commands;

use Chriha\ProjectCLI\Commands\Command;
use Chriha\ProjectCLI\Contracts\Plugin;
use Chriha\ProjectCLI\Helpers;
use Chriha\ProjectCLI\Services\Docker;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class DjangoAdminCommand extends Command
{

    /** @var string */
    protected static $defaultName = 'django:admin';

    /** @var string */
    protected $description = 'Run Django admin commands.';


    /**
     * Configure the command by adding a description, arguments and options
     *
     * @return void
     */
    public function configure() : void
    {
        $this->addDynamicArguments()->addDynamicOptions();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(Docker $docker) : void
    {
        $docker->exec('web', $this->getParameters(['django-admin']))
            ->setTty(true)
            ->run(
                function ($type, $buffer)
                {
                    $this->output->write($buffer);
                }
            );
    }

    /**
     * Make command only available if inside the project
     */
    public static function isActive() : bool
    {
        return PROJECT_IS_INSIDE
            && Helpers::isProjectType('django')
            && file_exists(Helpers::projectPath('src/manage.py'));
    }

}
