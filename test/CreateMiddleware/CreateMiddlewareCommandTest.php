<?php

/**
 * @see       https://github.com/mezzio/mezzio-tooling for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-tooling/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-tooling/blob/master/LICENSE.md New BSD License
 */

namespace MezzioTest\Tooling\CreateMiddleware;

use Mezzio\Tooling\CreateMiddleware\CreateMiddleware;
use Mezzio\Tooling\CreateMiddleware\CreateMiddlewareCommand;
use Mezzio\Tooling\CreateMiddleware\CreateMiddlewareException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use ReflectionMethod;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CreateMiddlewareCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function setUp()
    {
        $this->input = $this->prophesize(InputInterface::class);
        $this->output = $this->prophesize(ConsoleOutputInterface::class);

        $this->command = new CreateMiddlewareCommand('middleware:create');
    }

    private function reflectExecuteMethod()
    {
        $r = new ReflectionMethod($this->command, 'execute');
        $r->setAccessible(true);
        return $r;
    }

    public function testConfigureSetsExpectedDescription()
    {
        $this->assertContains('Create an http-interop middleware', $this->command->getDescription());
    }

    public function testConfigureSetsExpectedHelp()
    {
        $this->assertEquals(CreateMiddlewareCommand::HELP, $this->command->getHelp());
    }

    public function testConfigureSetsExpectedArguments()
    {
        $definition = $this->command->getDefinition();
        $this->assertTrue($definition->hasArgument('middleware'));
        $argument = $definition->getArgument('middleware');
        $this->assertTrue($argument->isRequired());
        $this->assertEquals(CreateMiddlewareCommand::HELP_ARG_MIDDLEWARE, $argument->getDescription());
    }

    public function testSuccessfulExecutionEmitsExpectedMessages()
    {
        $generator = Mockery::mock('overload:' . CreateMiddleware::class);
        $generator->shouldReceive('process')
            ->once()
            ->with('Foo\TestMiddleware')
            ->andReturn(__DIR__);

        $this->input->getArgument('middleware')->willReturn('Foo\TestMiddleware');
        $this->output
            ->writeln(Argument::containingString('Creating middleware Foo\TestMiddleware'))
            ->shouldBeCalled();
        $this->output
            ->writeln(Argument::containingString('Success'))
            ->shouldBeCalled();
        $this->output
            ->writeln(Argument::containingString('Created class Foo\TestMiddleware, in file ' . __DIR__))
            ->shouldBeCalled();

        $method = $this->reflectExecuteMethod();

        $this->assertSame(0, $method->invoke(
            $this->command,
            $this->input->reveal(),
            $this->output->reveal()
        ));
    }

    public function testAllowsExceptionsRaisedFromCreateMiddlewareToBubbleUp()
    {
        $generator = Mockery::mock('overload:' . CreateMiddleware::class);
        $generator->shouldReceive('process')
            ->once()
            ->with('Foo\TestMiddleware')
            ->andThrow(CreateMiddlewareException::class, 'ERROR THROWN');

        $this->input->getArgument('middleware')->willReturn('Foo\TestMiddleware');
        $this->output
            ->writeln(Argument::containingString('Creating middleware Foo\TestMiddleware'))
            ->shouldBeCalled();

        $this->output
            ->writeln(Argument::containingString('Success'))
            ->shouldNotBeCalled();

        $method = $this->reflectExecuteMethod();

        $this->expectException(CreateMiddlewareException::class);
        $this->expectExceptionMessage('ERROR THROWN');

        $method->invoke(
            $this->command,
            $this->input->reveal(),
            $this->output->reveal()
        );
    }
}
