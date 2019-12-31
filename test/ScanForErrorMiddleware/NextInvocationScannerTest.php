<?php

/**
 * @see       https://github.com/mezzio/mezzio-tooling for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-tooling/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-tooling/blob/master/LICENSE.md New BSD License
 */

namespace MezzioTest\Tooling\ScanForErrorMiddleware;

use Mezzio\Tooling\ScanForErrorMiddleware\NextInvocationScanner;
use PHPUnit\Framework\TestCase;

class NextInvocationScannerTest extends TestCase
{
    const METHOD_WITHOUT_NEXT = <<< 'EOC'
public function doSomething($request, $response)
{
}
EOC;

    const INVOKE_VARS_ONLY = <<< 'EOC'
public function doSomething($request, $response, $next)
{
    return $next($request, $response%s);
}
EOC;

    const INVOKE_REQUEST_WITH = <<< 'EOC'
public function doSomething($request, $response, $next)
{
    return $next($request->withHeader('X-Foo', 'Bar'), $response%s);
}
EOC;

    const INVOKE_RESPONSE_WITH = <<< 'EOC'
public function doSomething($request, $response, $next)
{
    return $next($request, $response->withHeader('X-Foo', 'Bar')%s);
}
EOC;

    const INVOKE_BOTH_WITH = <<< 'EOC'
public function doSomething($request, $response, $next)
{
    return $next($request->withHeader('X-Foo', 'Bar'), $response->withHeader('X-Foo', 'Bar')%s);
}
EOC;

    const INVOKE_REQUEST_MULTIPLE_WITH = <<< 'EOC'
public function doSomething($request, $response, $next)
{
    return $next(
        $request
            ->withMethod('POST')
            ->withHeader('X-Foo', 'Bar'),
        $response%s
    );
}
EOC;

    const INVOKE_RESPONSE_MULTIPLE_WITH = <<< 'EOC'
public function doSomething($request, $response, $next)
{
    return $next(
        $request,
        $response
            ->withStatus(500)
            ->withHeader('X-Foo', 'Bar')%s
    );
}
EOC;

    const INVOKE_BOTH_MULTIPLE_WITH = <<< 'EOC'
public function doSomething($request, $response, $next)
{
    return $next(
        $request
            ->withMethod('POST')
            ->withHeader('X-Foo', 'Bar'),
        $response
            ->withStatus(500)
            ->withHeader('X-Foo', 'Bar')%s
    );
}
EOC;

    public function testMethodWithoutANextArgumentMarksAsFalse()
    {
        $scanner = new NextInvocationScanner(self::METHOD_WITHOUT_NEXT);
        $this->assertFalse($scanner->scan());
    }

    public function messageVariablesProvider()
    {
        return [
            'vars-only'              => [self::INVOKE_VARS_ONLY],
            'request-with'           => [self::INVOKE_REQUEST_WITH],
            'response-with'          => [self::INVOKE_RESPONSE_WITH],
            'both-with'              => [self::INVOKE_BOTH_WITH],
            'request-multiple-with'  => [self::INVOKE_REQUEST_MULTIPLE_WITH],
            'response-multiple-with' => [self::INVOKE_RESPONSE_MULTIPLE_WITH],
            'both-multiple-with'     => [self::INVOKE_BOTH_MULTIPLE_WITH],
        ];
    }

    /**
     * @dataProvider messageVariablesProvider
     *
     * @param string $method
     */
    public function testReturnsFalseWhenNextIsInvokedWithoutAnErrorArgument($method)
    {
        $method = sprintf($method, '');
        $scanner = new NextInvocationScanner($method);
        $this->assertFalse($scanner->scan());
    }

    /**
     * @dataProvider messageVariablesProvider
     *
     * @param string $method
     */
    public function testReturnsTrueWhenNextIsInvokedWithAnErrorArgument($method)
    {
        $method = sprintf($method, ", 'error'");
        $scanner = new NextInvocationScanner($method);
        $this->assertTrue($scanner->scan());
    }
}
