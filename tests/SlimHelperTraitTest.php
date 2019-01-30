<?php


namespace Tests;


use AndrewBreksa\SlimActionHelpers\AbstractAction;
use AndrewBreksa\SlimActionHelpers\ActionInterface;
use NilPortugues\Api\Problem\ApiProblem;
use NilPortugues\Api\Problem\ApiProblemResponse;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class SlimHelperTraitTest extends TestCase
{
    public function testGetQueryParam()
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getQueryParams')->andReturn([], ['foo' => 'bar']);
        $response = \Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('withStatus')->once();

        $clazz = new class(\Mockery::mock(ContainerInterface::class), []) extends AbstractAction
        {
            /**
             * @var TestCase
             */
            private $test;

            /**
             * @return TestCase
             */
            public function getTest(): TestCase
            {
                return $this->test;
            }

            /**
             * @param TestCase $test
             * @return
             */
            public function setTest(TestCase $test)
            {
                $this->test = $test;
                return $this;
            }

            /**
             * @param ServerRequestInterface $request
             * @param ResponseInterface $response
             * @param array $args
             * @return mixed|null
             */
            public function act()
            {
                $this->getTest()->assertEquals(123, $this->getQueryParam('foo', 123));
                $this->getTest()->assertEquals('bar', $this->getQueryParam('foo', 123));
                return $this->getResponse()->withStatus(456);
            }
        };

        $clazz->setTest($this);

        $clazz->__invoke($request, $response, []);

        \Mockery::close();
    }

    public function testGetRouteParam()
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getAttribute')->withArgs(['route'])->once()->andReturn('baz');
        $request->shouldReceive('getAttribute')->withArgs(['routeInfo'])->andReturn([
            null,
            null,
            []
        ],
            [
                null,
                null,
                [
                    'foo' => 'bar'
                ]
            ]);
        $response = \Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('withStatus')->once();

        $clazz = new class(\Mockery::mock(ContainerInterface::class), []) extends AbstractAction
        {
            /**
             * @var TestCase
             */
            private $test;

            /**
             * @return TestCase
             */
            public function getTest(): TestCase
            {
                return $this->test;
            }

            /**
             * @param TestCase $test
             * @return
             */
            public function setTest(TestCase $test)
            {
                $this->test = $test;
                return $this;
            }

            /**
             * @param ServerRequestInterface $request
             * @param ResponseInterface $response
             * @param array $args
             * @return mixed|null
             */
            public function act()
            {
                $this->getTest()->assertEquals(123, $this->getRouteParam('foo', 123));
                $this->getTest()->assertEquals('bar', $this->getRouteParam('foo', 123));
                $this->getTest()->assertEquals('baz', $this->getRoute());
                return $this->getResponse()->withStatus(456);
            }
        };

        $clazz->setTest($this);

        $clazz->__invoke($request, $response, []);

        \Mockery::close();
    }

    public function testText()
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getQueryParams')->andReturn([], ['foo' => 'bar']);
        $response = \Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('withStatus')->once()->andReturnSelf();
        $response->shouldReceive('withHeader')->withArgs(['Content-Type', 'text/plain'])->andReturnSelf();
        $response->shouldReceive('getBody')->andReturnUsing(function () {
            $mock = \Mockery::mock(StreamInterface::class);
            $mock->shouldReceive('write')->once();
            return $mock;
        })->andReturnSelf();

        $clazz = new class(\Mockery::mock(ContainerInterface::class), []) extends AbstractAction
        {
            /**
             * @var TestCase
             */
            private $test;

            /**
             * @return TestCase
             */
            public function getTest(): TestCase
            {
                return $this->test;
            }

            /**
             * @param TestCase $test
             * @return
             */
            public function setTest(TestCase $test)
            {
                $this->test = $test;
                return $this;
            }

            /**
             * @param ServerRequestInterface $request
             * @param ResponseInterface $response
             * @param array $args
             * @return mixed|null
             */
            public function act()
            {
                return $this->text('testing 123');
            }
        };

        $clazz->setTest($this);

        $this->assertTrue($clazz->__invoke($request, $response, []) instanceof ResponseInterface);

        \Mockery::close();
    }

    public function testHtml()
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getQueryParams')->andReturn([], ['foo' => 'bar']);
        $response = \Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('withStatus')->once()->withArgs([404])->andReturnSelf();
        $response->shouldReceive('withHeader')->withArgs(['Content-Type', 'text/html'])->andReturnSelf();
        $response->shouldReceive('getBody')->andReturnUsing(function () {
            $mock = \Mockery::mock(StreamInterface::class);
            $mock->shouldReceive('write')->once()->withArgs(['testing 123']);
            return $mock;
        })->andReturnSelf();

        $clazz = new class(\Mockery::mock(ContainerInterface::class), []) extends AbstractAction
        {
            /**
             * @var TestCase
             */
            private $test;

            /**
             * @return TestCase
             */
            public function getTest(): TestCase
            {
                return $this->test;
            }

            /**
             * @param TestCase $test
             * @return
             */
            public function setTest(TestCase $test)
            {
                $this->test = $test;
                return $this;
            }

            /**
             * @param ServerRequestInterface $request
             * @param ResponseInterface $response
             * @param array $args
             * @return mixed|null
             */
            public function act()
            {
                return $this->html('testing 123', 404);
            }
        };

        $clazz->setTest($this);

        $this->assertTrue($clazz->__invoke($request, $response, []) instanceof ResponseInterface);

        \Mockery::close();
    }

    public function testJson()
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getQueryParams')->andReturn([], ['foo' => 'bar']);
        $response = \Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('withStatus')->once()->withArgs([404])->andReturnSelf();
        $response->shouldReceive('withHeader')->withArgs(['Content-Type', 'application/json'])->andReturnSelf();
        $response->shouldReceive('getBody')->andReturnUsing(function () {
            $mock = \Mockery::mock(StreamInterface::class);
            $mock->shouldReceive('write')->once()->withArgs([json_encode(['foo' => 'bar'])]);
            return $mock;
        })->andReturnSelf();

        $clazz = new class(\Mockery::mock(ContainerInterface::class), []) extends AbstractAction
        {
            /**
             * @var TestCase
             */
            private $test;

            /**
             * @return TestCase
             */
            public function getTest(): TestCase
            {
                return $this->test;
            }

            /**
             * @param TestCase $test
             * @return
             */
            public function setTest(TestCase $test)
            {
                $this->test = $test;
                return $this;
            }

            /**
             * @param ServerRequestInterface $request
             * @param ResponseInterface $response
             * @param array $args
             * @return mixed|null
             */
            public function act()
            {
                return $this->json([
                    'foo' => 'bar'
                ], 404);
            }
        };

        $clazz->setTest($this);

        $this->assertTrue($clazz->__invoke($request, $response, []) instanceof ResponseInterface);

        \Mockery::close();
    }

    public function testView()
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $container = \Mockery::mock(ContainerInterface::class)->shouldReceive('get')->withArgs(['view'])->andReturnUsing(function () {
            $v = \Mockery::mock()
                ->shouldReceive('render')->withArgs(['foo/bar', [
                    'fizz' => 'buzz'
                ]])->andReturn(\Mockery::mock(ResponseInterface::class));
            return $v->getMock();
        })
            ->once()
            ->getMock();
        $response = \Mockery::mock(ResponseInterface::class);

        $clazz = new class($container, ['foo' => 'bar']) extends AbstractAction
        {
            /**
             * @var TestCase
             */
            private $test;

            /**
             * @return TestCase
             */
            public function getTest(): TestCase
            {
                return $this->test;
            }

            /**
             * @param TestCase $test
             * @return
             */
            public function setTest(TestCase $test)
            {
                $this->test = $test;
                return $this;
            }

            /**
             * @param ServerRequestInterface $request
             * @param ResponseInterface $response
             * @param array $args
             * @return mixed|null
             */
            public function act()
            {
                $this->getTest()->assertTrue($this->getRequest() instanceof ServerRequestInterface);
                $this->getTest()->assertEquals([
                    'foo' => 'bar2'
                ], $this->getOptions());
                return $this->view('foo/bar', [
                    'fizz' => 'buzz'
                ]);
            }
        };

        $this->assertTrue((
            $clazz->setContainer($container)
            ) instanceof ActionInterface);

        $this->assertEquals([
            'foo' => 'bar'
        ], $clazz->getOptions());

        $this->assertTrue((
            $clazz->setOptions([
                'foo' => 'bar2'
            ])
            ) instanceof ActionInterface);

        $clazz->setTest($this);

        $this->assertTrue($clazz->__invoke($request, $response, []) instanceof ResponseInterface);

        \Mockery::close();
    }

    public function testProblem()
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);

        $clazz = new class(\Mockery::mock(ContainerInterface::class), []) extends AbstractAction
        {
            /**
             * @var TestCase
             */
            private $test;

            /**
             * @return TestCase
             */
            public function getTest(): TestCase
            {
                return $this->test;
            }

            /**
             * @param TestCase $test
             * @return
             */
            public function setTest(TestCase $test)
            {
                $this->test = $test;
                return $this;
            }

            /**
             * @param ServerRequestInterface $request
             * @param ResponseInterface $response
             * @param array $args
             * @return mixed|null
             */
            public function act()
            {
                return $this->problem(500, 'User data is not valid.', 'Input values do not match the requirements', 'user.invalid_data');
            }
        };

        $clazz->setTest($this);

        $problem_resp = $clazz->__invoke($request, $response, []);

        $this->assertTrue($problem_resp instanceof ResponseInterface);
        $this->assertEquals(500, $problem_resp->getStatusCode());
        $this->assertEquals(json_encode([
            'title' => 'Input values do not match the requirements',
            'status' => 500,
            'detail' => 'User data is not valid.',
            'type' => 'user.invalid_data',
        ], JSON_PRETTY_PRINT), $problem_resp->getBody()->getContents());

        \Mockery::close();
    }

    public function testException()
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);

        $clazz = new class(\Mockery::mock(ContainerInterface::class), []) extends AbstractAction
        {
            /**
             * @var TestCase
             */
            private $test;

            /**
             * @return TestCase
             */
            public function getTest(): TestCase
            {
                return $this->test;
            }

            /**
             * @param TestCase $test
             * @return
             */
            public function setTest(TestCase $test)
            {
                $this->test = $test;
                return $this;
            }

            /**
             * @param ServerRequestInterface $request
             * @param ResponseInterface $response
             * @param array $args
             * @return mixed|null
             */
            public function act()
            {
                $ex = new \Exception('User data is not valid.', 500);
                return $this->exception($ex);
            }
        };

        $clazz->setTest($this);

        $problem_resp = $clazz->__invoke($request, $response, []);

        $this->assertTrue($problem_resp instanceof ResponseInterface);
        $this->assertEquals(500, $problem_resp->getStatusCode());
        $this->assertArraySubset([
            'title' => 'Internal Server Error',
            'status' => 500,
            'detail' => 'User data is not valid.',
        ], json_decode($problem_resp->getBody()->getContents(), true));

        \Mockery::close();
    }

}