<?php

/**
 * @see       https://github.com/mezzio/mezzio-tooling for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-tooling/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-tooling/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Mezzio\Tooling\Module;

use Laminas\ComponentInstaller\Injector\ConfigAggregatorInjector;
use Laminas\ComposerAutoloading\Command\Disable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeregisterCommand extends Command
{
    const HELP = <<< 'EOT'
Deregister an existing middleware module from the application, by:

- Removing the associated PSR-4 autoloader entry from composer.json, and
  regenerating autoloading rules.
- Removing the associated ConfigProvider class for the module from the
  application configuration.
EOT;

    const HELP_ARG_MODULE = 'The module to register with the application';

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this->setDescription('Deregister a middleware module from the application');
        $this->setHelp(self::HELP);
        CommandCommonOptions::addDefaultOptionsAndArguments($this);
    }

    /**
     * Deregister module.
     *
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $module = $input->getArgument('module');
        $composer = $input->getOption('composer') ?: 'composer';
        $modulesPath = CommandCommonOptions::getModulesPath($input);

        $injector = new ConfigAggregatorInjector(getcwd());
        $configProvider = sprintf('%s\ConfigProvider', $module);
        if ($injector->isRegistered($configProvider)) {
            $injector->remove($configProvider);
        }

        $disable = new Disable(getcwd(), $modulesPath, $composer);
        $disable->process($module);

        $output->writeln(sprintf('Removed autoloading rules and configuration entries for module %s', $module));
        return 0;
    }
}
