<?php

/**
 * @see       https://github.com/mezzio/mezzio-tooling for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-tooling/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-tooling/blob/master/LICENSE.md New BSD License
 */

namespace MezzioTest\Tooling\GenerateProgrammaticPipelineFromConfig;

use Mezzio\Tooling\GenerateProgrammaticPipelineFromConfig\Generator;
use Mezzio\Tooling\GenerateProgrammaticPipelineFromConfig\GeneratorException;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase as TestCase;

class GeneratorTest extends TestCase
{
    public function setUp()
    {
        $this->dir = vfsStream::setup('project');
        $this->generator = new Generator();
        $this->generator->projectDir = vfsStream::url('project');
    }

    public function generatePipeline()
    {
        vfsStream::newFile('config/config.php')
            ->at($this->dir)
            ->setContent(file_get_contents(__DIR__ . '/TestAsset/asset/config/config.php'));

        vfsStream::newFile('public/index.php')
            ->at($this->dir)
            ->setContent(file_get_contents(__DIR__ . '/TestAsset/asset/public/index.php'));

        vfsStream::newDirectory('config/autoload', 0755)
            ->at($this->dir);
    }

    public function testGeneratesExpectedPipeline()
    {
        $this->generatePipeline();

        $configFile = vfsStream::url('project/config/config.php');
        $this->generator->process($configFile);

        $pipelineFile = vfsStream::url('project/config/pipeline.php');
        $this->assertTrue(file_exists($pipelineFile));
        $this->assertEquals(
            file_get_contents(__DIR__ . '/TestAsset/expected/config/pipeline.php'),
            file_get_contents($pipelineFile),
            'Generated pipeline does not match expected pipeline'
        );

        $routesFile = vfsStream::url('project/config/routes.php');
        $this->assertTrue(file_exists($routesFile));
        $this->assertEquals(
            file_get_contents(__DIR__ . '/TestAsset/expected/config/routes.php'),
            file_get_contents($routesFile),
            'Generated routing does not match expected routing'
        );

        $pipelineConfigFile = vfsStream::url('project/config/autoload/programmatic-pipeline.global.php');
        $this->assertTrue(file_exists($pipelineConfigFile));
        $this->assertEquals(
            file_get_contents(__DIR__ . '/TestAsset/expected/config/autoload/programmatic-pipeline.global.php'),
            file_get_contents($pipelineConfigFile),
            'Generated pipeline config does not match expected config'
        );

        $application = file_get_contents(vfsStream::url('project/public/index.php'));
        $this->assertEquals(
            file_get_contents(__DIR__ . '/TestAsset/expected/public/index.php'),
            file_get_contents(vfsStream::url('project/public/index.php')),
            'Generated public/index.php does not match expected'
        );
    }

    public function testRaisesExceptionIfConfigFileNotFound()
    {
        $configFile = vfsStream::url('project/config/config.php');
        $this->setExpectedException(GeneratorException::class, 'not found');
        $this->generator->process($configFile);
    }

    public function testRaisesExceptionIfConfigFileNotReadable()
    {
        vfsStream::newFile('config/config.php', 0111)
            ->at($this->dir);
        $configFile = vfsStream::url('project/config/config.php');
        $this->setExpectedException(GeneratorException::class, 'not readable');
        $this->generator->process($configFile);
    }

    public function testRaisesExceptionIfConfigFileDoesNotReturnArray()
    {
        vfsStream::newFile('config/config.php')
            ->at($this->dir)
            ->setContent('<' . '?php /* NO RETURN */');
        $configFile = vfsStream::url('project/config/config.php');
        $this->setExpectedException(GeneratorException::class, 'did not return an array');
        $this->generator->process($configFile);
    }

    public function invalidMiddleware()
    {
        $pipelineConfigTemplate = <<< 'EOT'
<?php
return [
    'middleware_pipeline' => [
        [
            'middleware' => %s,
        ],
    ],
];
EOT;
        return [
            'int'    => [sprintf($pipelineConfigTemplate, '1')],
            'float'  => [sprintf($pipelineConfigTemplate, '1.1')],
            'object' => [sprintf($pipelineConfigTemplate, '(object) [\'foo\' => \'bar\']')],
        ];
    }

    /**
     * @dataProvider invalidMiddleware
     */
    public function testRaisesExceptionIfMiddlewareInPipelineIsInvalid($pipelineConfig)
    {
        vfsStream::newFile('config/config.php')
            ->at($this->dir)
            ->setContent($pipelineConfig);
        $configFile = vfsStream::url('project/config/config.php');

        $this->setExpectedException(GeneratorException::class, 'middleware specification');
        $this->generator->process($configFile);
    }

    public function testRaisesExceptionIfApplicationBootstrapIsMissingRunStatement()
    {
        vfsStream::newFile('config/config.php')
            ->at($this->dir)
            ->setContent(file_get_contents(__DIR__ . '/TestAsset/asset/config/config.php'));

        $publicIndex = file_get_contents(__DIR__ . '/TestAsset/asset/public/index.php');
        $publicIndex = str_replace('$app->run();', '', $publicIndex);

        vfsStream::newFile('public/index.php')
            ->at($this->dir)
            ->setContent($publicIndex);

        vfsStream::newDirectory('config/autoload', 0755)
            ->at($this->dir);
        $configFile = vfsStream::url('project/config/config.php');

        $this->setExpectedException(GeneratorException::class, '$app->run');
        $this->generator->process($configFile);
    }

    public function processArtifacts()
    {
        return [
            'pipeline' => ['config/pipeline.php'],
            'routes'   => ['config/routes.php'],
        ];
    }

    /**
     * @dataProvider processArtifacts
     */
    public function testProcessRaisesExceptionIfArtifactsFromPreviousRunArePresent($filename)
    {
        $this->generatePipeline();

        vfsStream::newFile($filename)
            ->at($this->dir)
            ->setContent('<' . "?php\nreturn [];");

        $configFile = vfsStream::url('project/config/config.php');

        $this->setExpectedException(GeneratorException::class, 'previous run detected');
        $this->generator->process($configFile);
    }
}
