<?php

/**
 * @see       https://github.com/mezzio/mezzio-tooling for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-tooling/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-tooling/blob/master/LICENSE.md New BSD License
 */

namespace MezzioTest\Tooling\MigrateOriginalMessageCalls;

use Laminas\Stdlib\ConsoleHelper;
use Mezzio\Tooling\MigrateOriginalMessageCalls\Command;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Argument;

class CommandTest extends TestCase
{
    use ProjectSetupTrait;

    const TEST_COMMAND_NAME = 'migrate-original-message-calls';

    public function assertHelpOutput($console, $resource = STDOUT, $command = self::TEST_COMMAND_NAME)
    {
        $console->writeLine(
            Argument::containingString($command . ' [command] [options]'),
            true,
            $resource
        )->shouldBeCalled();
    }

    public function helpRequests()
    {
        return [
            'no-args'                  => [[]],
            'help-command'             => [['help']],
            'help-option'              => [['--help']],
            'help-flag'                => [['-h']],
            'scan-command-help-option' => [['scan', '--help']],
            'scan-command-help-flag'   => [['scan', '-h']],
        ];
    }

    /**
     * @dataProvider helpRequests
     */
    public function testHelpRequestsEmitHelpToStdout($args)
    {
        $console = $this->prophesize(ConsoleHelper::class);
        $this->assertHelpOutput($console);

        $command = new Command(self::TEST_COMMAND_NAME, $console->reveal());
        $this->assertEquals(0, $command->process($args));
    }

    public function testUnknownCommandEmitsHelpToStderrWithErrorMessage()
    {
        $console = $this->prophesize(ConsoleHelper::class);
        $console->writeLine(
            Argument::containingString('Unknown command'),
            true,
            STDERR
        )->shouldBeCalled();
        $this->assertHelpOutput($console, STDERR);

        $command = new Command(self::TEST_COMMAND_NAME, $console->reveal());
        $this->assertEquals(1, $command->process(['foo', 'bar']));
    }

    public function testCommandErrorIfUnknownOptionsProvidedToScan()
    {
        $console = $this->prophesize(ConsoleHelper::class);
        $console->writeLine(
            Argument::containingString('Unable to determine src directory: Invalid options provided'),
            true,
            STDERR
        )->shouldBeCalled();
        $this->assertHelpOutput($console, STDERR);

        $command = new Command(self::TEST_COMMAND_NAME, $console->reveal());
        $this->assertEquals(1, $command->process(['scan', 'invalid']));
    }

    public function testCommandErrorIfSrcOptionProvidedToScanIsMissingValue()
    {
        $console = $this->prophesize(ConsoleHelper::class);
        $console->writeLine(
            Argument::containingString('Unable to determine src directory: --src was missing'),
            true,
            STDERR
        )->shouldBeCalled();
        $this->assertHelpOutput($console, STDERR);

        $command = new Command(self::TEST_COMMAND_NAME, $console->reveal());
        $this->assertEquals(1, $command->process(['scan', '--src']));
    }

    public function testCommandErrorIfSrcOptionProvidedToScanIsNotADirectory()
    {
        $console = $this->prophesize(ConsoleHelper::class);
        $console->writeLine(
            Argument::containingString('Unable to determine src directory: Invalid --src argument'),
            true,
            STDERR
        )->shouldBeCalled();
        $this->assertHelpOutput($console, STDERR);

        $command = new Command(self::TEST_COMMAND_NAME, $console->reveal());
        $this->assertEquals(1, $command->process(['scan', '--src', 'NOT-A-DIRECTORY']));
    }

    public function testCommandEmitsExpectedDataForSuccessfulExecution()
    {
        $dir = vfsStream::setup('migrate');
        $this->setupSrcDir($dir);
        $path = vfsStream::url('migrate');

        $console = $this->setupConsoleHelper();
        $console->writeLine(
            Argument::containingString('Scanning for usage of Stratigility HTTP message decorators...')
        )->shouldBeCalled();
        $console->writeLine(
            Argument::containingString('One or more files contained calls to getOriginalResponse()')
        )->shouldBeCalled();
        $console->writeLine(
            Argument::containingString('Check the above logs')
        )->shouldBeCalled();
        $console->writeLine(
            Argument::containingString(Command::TEMPLATE_RESPONSE_DETAILS)
        )->shouldBeCalled();
        $console->writeLine(
            Argument::containingString('Done!')
        )->shouldBeCalled();

        $command = new Command(self::TEST_COMMAND_NAME, $console->reveal());
        $this->assertEquals(0, $command->process(['scan', '--src', $path]));
    }
}
