<?php

/**
 * @see       https://github.com/mezzio/mezzio-tooling for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-tooling/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-tooling/blob/master/LICENSE.md New BSD License
 */

namespace Mezzio\Tooling\GenerateProgrammaticPipelineFromConfig;

use Laminas\Stdlib\ConsoleHelper;

class Help
{
    const TEMPLATE = <<< 'EOT'
<info>Usage:</info>

  %s <command> [options]

<info>Commands:</info>

  <info>help</info>             Display this help/usage message
  <info>generate</info>         Generate a programmatic pipeline from configuration

<info>Options:</info>

  <info>--help|-h</info>        Display this help/usage message
  <info>--config-file</info>    Specify a path to the configuration file; defaults
                   to config/config.php. The file is expected to return
                   a PHP array value containing all configuration.

Reads existing configuration from the --config-file, and uses that
information to generate a programmatic middleware pipeline and routing
table for your application. The information is written to STDOUT, and
can then be piped to a file or copy and pasted into your
public/index.php file.
  
EOT;

    /**
     * @var string
     */
    private $command;

    /**
     * @var ConsoleHelper
     */
    private $helper;

    /**
     * @param string $command Name of script invoking the command.
     * @param ConsoleHelper $helper
     */
    public function __construct($command, ConsoleHelper $helper)
    {
        $this->command = $command;
        $this->helper = $helper;
    }

    /**
     * Emit the help message.
     *
     * @param resource $resource Stream to which to write; defaults to STDOUT.
     * @return void
     */
    public function __invoke($resource = STDOUT)
    {
        // Use basename of command if it is a realpath
        $command = (file_exists($this->command) && realpath($this->command) === $this->command)
            ? basename($this->command)
            : $this->command;

        $this->helper->writeLine(sprintf(
            self::TEMPLATE,
            $command
        ), true, $resource);
    }
}
