<?php

/**
 * @see       https://github.com/mezzio/mezzio-tooling for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-tooling/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-tooling/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Mezzio\Tooling\CreateHandler;

/**
 * Create a request handler
 *
 * Creates a request handler class file for a given class in a given project root.
 */
class CreateHandler
{
    /**
     * @var string Template for request handler class.
     */
    const CLASS_SKELETON = <<< 'EOS'
<?php

namespace %namespace%;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class %class% implements RequestHandlerInterface
{
    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        // Create and return a response
    }
}
EOS;

    /**
     * @param string $class
     * @param string|null $projectRoot
     * @param string $classSkeleton
     * @return string
     * @throws CreateHandlerException
     */
    public function process($class, $projectRoot = null, $classSkeleton = self::CLASS_SKELETON)
    {
        $projectRoot = $projectRoot ?: getcwd();

        $path = $this->getClassPath($class, $projectRoot);

        list($namespace, $class) = $this->getNamespaceAndClass($class);

        $content = str_replace(
            ['%namespace%', '%class%'],
            [$namespace, $class],
            $classSkeleton
        );

        if (is_file($path)) {
            throw CreateHandlerException::classExists($path, $class);
        }

        file_put_contents($path, $content);
        return $path;
    }

    /**
     * @param string $class
     * @param string $projectRoot
     * @return string
     * @throws CreateHandlerException
     */
    private function getClassPath($class, $projectRoot)
    {
        $autoloaders = $this->getComposerAutoloaders($projectRoot);
        list($namespace, $path) = $this->discoverNamespaceAndPath($class, $autoloaders);

        // Absolute path to namespace
        $path = implode([$projectRoot, DIRECTORY_SEPARATOR, $path]);

        $parts = explode('\\', $class);
        $className = array_pop($parts);

        // Create absolute path to subnamespace, if required
        $nsParts = explode('\\', trim($namespace, '\\'));
        $subNsParts = array_slice($parts, count($nsParts));

        if (0 < count($subNsParts)) {
            $subNsPath = implode(DIRECTORY_SEPARATOR, $subNsParts);
            $path = implode([$path, DIRECTORY_SEPARATOR, $subNsPath]);
        }

        // Create path if it does not exist
        if (! is_dir($path)) {
            if (false === mkdir($path, 0755, true)) {
                throw CreateHandlerException::unableToCreatePath($path, $class);
            }
        }

        return $path . DIRECTORY_SEPARATOR . $className . '.php';
    }

    /**
     * @param string $projectRoot
     * @return array Associative array of namespace/path pairs
     * @throws CreateHandlerException
     */
    private function getComposerAutoloaders($projectRoot)
    {
        $composerPath = sprintf('%s/composer.json', $projectRoot);
        if (! file_exists($composerPath)) {
            throw CreateHandlerException::missingComposerJson();
        }

        $composer = json_decode(file_get_contents($composerPath), true);

        if (json_last_error() !== \JSON_ERROR_NONE) {
            throw CreateHandlerException::invalidComposerJson(json_last_error_msg());
        }

        if (! isset($composer['autoload']['psr-4'])) {
            throw CreateHandlerException::missingComposerAutoloaders();
        }

        if (! is_array($composer['autoload']['psr-4'])) {
            throw CreateHandlerException::missingComposerAutoloaders();
        }

        return $composer['autoload']['psr-4'];
    }

    /**
     * @param string $class
     * @param array $autoloaders
     * @return array [namespace, path]
     * @throws CreateHandlerException
     */
    private function discoverNamespaceAndPath($class, array $autoloaders)
    {
        foreach ($autoloaders as $namespace => $path) {
            if (0 === strpos($class, $namespace)) {
                $path = trim(
                    str_replace(
                        ['/', '\\'],
                        DIRECTORY_SEPARATOR,
                        $path
                    ),
                    DIRECTORY_SEPARATOR
                );
                return [$namespace, $path];
            }
        }

        throw CreateHandlerException::autoloaderNotFound($class);
    }

    /**
     * @param string $class
     * @return array [namespace, class]
     */
    private function getNamespaceAndClass($class)
    {
        $parts = explode('\\', $class);
        $className = array_pop($parts);
        $namespace = implode('\\', $parts);
        return [$namespace, $className];
    }
}
