<?php


namespace Tests;


use AndrewBreksa\SlimActionHelpers\AbstractAction;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AbstractActionTest extends TestCase
{
    public function testInvoke()
    {
        $clazz = new class(\Mockery::mock(ContainerInterface::class), []) extends AbstractAction
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
        $result = $clazz->__invoke($request, $response, []);
        $this->assertTrue($result instanceof ResponseInterface);
        $this->assertEquals(456, $result->getStatusCode());
    }
}