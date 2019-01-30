<?php


namespace Tests;


use AndrewBreksa\SlimActionHelpers\AbstractAction;
use AndrewBreksa\SlimActionHelpers\AbstractMiddleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use phpDocumentor\Reflection\Types\Callable_;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AbstractMiddlewareTest extends TestCase
{
    public function testInvokeWithResponse()
    {
        $clazz = new class(\Mockery::mock(ContainerInterface::class), []) extends AbstractMiddleware
        {
            /**
             * @param ServerRequestInterface $request
             * @param ResponseInterface $response
             * @param array $args
             * @return mixed|null
             */
            public function act()
            {
                return $this->getResponse()->withStatus(456);
            }
        };

        $request = ServerRequest::fromGlobals();
        $response = new Response();
        $result = $clazz->__invoke($request, $response, function ($request, $response) {
            return $response;
        });
        $this->assertTrue($result instanceof ResponseInterface);
        $this->assertEquals(456, $result->getStatusCode());
    }

    public function testInvokeWithNoResponse()
    {
        $clazz = new class(\Mockery::mock(ContainerInterface::class), []) extends AbstractMiddleware
        {
            /**
             * @param ServerRequestInterface $request
             * @param ResponseInterface $response
             * @param array $args
             * @return mixed|null
             */
            public function act()
            {
                return null;
            }
        };

        $request = ServerRequest::fromGlobals();
        $response = new Response();
        $result = $clazz->__invoke($request, $response, function ($request, $response) {
            return $response->withStatus(564);
        });
        $this->assertTrue($result instanceof ResponseInterface);
        $this->assertEquals(564, $result->getStatusCode());
    }

    public function testGetSetNext()
    {
        $clazz = new class(\Mockery::mock(ContainerInterface::class), []) extends AbstractMiddleware
        {
            /**
             * @param ServerRequestInterface $request
             * @param ResponseInterface $response
             * @param array $args
             * @return mixed|null
             */
            public function act()
            {
                return null;
            }
        };

        $clazz->setNext(function ($request, $response) {
            return $response->withStatus(564);
        });
        $this->assertTrue(is_callable($clazz->getNext()));
    }
}