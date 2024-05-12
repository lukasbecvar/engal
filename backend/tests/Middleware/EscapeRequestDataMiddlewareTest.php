<?php

namespace App\Tests\Middleware;

use App\Util\SecurityUtil;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use App\Middleware\EscapeRequestDataMiddleware;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Class EscapeRequestDataMiddlewareTest
 *
 * Unit test class for the EscapeRequestDataMiddleware.
 *
 * @package App\Tests\Middleware
 */
class EscapeRequestDataMiddlewareTest extends TestCase
{
    /**
     * Test whether the middleware correctly escapes request data.
     *
     * This test verifies that the middleware properly escapes data in the request.
     */
    public function testRequestDataEscape(): void
    {
        // create a mock object for SecurityUtil
        $securityUtil = $this->createMock(SecurityUtil::class);

        // define the behavior of the escapeString method of the mock object
        $securityUtil->expects($this->any())
            ->method('escapeString')
            ->willReturnCallback(function ($value) {
                return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);
            });

        // create an instance of EscapeRequestDataMiddleware with the mock SecurityUtil
        $middleware = new EscapeRequestDataMiddleware($securityUtil);

        // create a mock object for RequestEvent
        $requestEvent = $this->createMock(RequestEvent::class);

        // create a test instance of Request with a test URL and GET parameter containing dangerous HTML tags
        $request = Request::create('/test', 'GET', ['idk' => '<script>alert("Hello!");</script>']);

        // define the behavior of getRequest method of the mock RequestEvent to return the test Request
        $requestEvent->expects($this->any())->method('getRequest')->willReturn($request);

        // call the onKernelRequest method on the EscapeRequestDataMiddleware instance with the mock RequestEvent
        $middleware->onKernelRequest($requestEvent);

        // assert output
        $this->assertEquals('&lt;script&gt;alert(&quot;Hello!&quot;);&lt;/script&gt;', $request->query->get('idk'));
    }
}
