<?php

namespace AndrewBreksa\SlimActionHelpers;


use Exception;
use NilPortugues\Api\Problem\ApiProblemResponse;
use Projek\Slim\Plates;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Route;

/**
 * Trait SuperSlimTrait
 * @package AndrewBreksa\BFrame\Web
 */
trait SlimHelperTrait
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $options = [];

    public function __construct(ContainerInterface $ci, array $options = [])
    {
        $this->container = $ci;
        $this->options = $options;
    }

    public function bootstrap(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->setResponse($response);
        $this->setRequest($request);
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $key
     * @param null $default
     *
     * @return mixed|null
     * @internal param ServerRequestInterface $request
     */
    public function getQueryParam($key, $default = NULL)
    {
        if (!array_key_exists($key, $this->request->getQueryParams())) {
            return $default;
        }

        return $this->request->getQueryParams()[$key];
    }

    public function html($string, int $status = 200, $content_type = 'text/html')
    {
        return $this->text($string, $status, $content_type);
    }

    public function text(string $string, int $status = 200, $content_type = 'text/plain')
    {
        $response = $this->response;
        $response->getBody()->write($string);
        $response = $response->withHeader('Content-Type', $content_type);
        $response = $response->withStatus($status);
        $this->setResponse($response);

        return $response;
    }

    public function json($object, int $status = 200, $callback = null, $content_type = 'application/json')
    {
        if (is_null($callback)) {
            $callback = function ($object) {
                return json_encode($object);
            };
        }
        return $this->text(call_user_func_array($callback, [$object]), $status, $content_type);
    }

    public function problem($code, $detail, $title, $type, $additional_details = [])
    {
        return ApiProblemResponse::json($code, $detail, $title,
            $type, $additional_details);
    }

    public function exception(Exception $exception)
    {
        return ApiProblemResponse::fromExceptionToJson($exception);
    }

    public function view($template, array $vars)
    {
        /**
         * @var $pl Plates
         */
        $pl = $this->getContainer()->get('view');
        return $pl->render($template, $vars);
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param ContainerInterface $container
     * @return SlimHelperTrait
     */
    public function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }

    public function getRouteParam($key, $default = null)
    {
        $d = $this->request->getAttribute('routeInfo')[2];
        if (!array_key_exists($key, $d)) {
            return $default;
        }
        return $d[$key];
    }

    /**
     * @return Route
     */
    public function getRoute()
    {
        return $this->request->getAttribute('route');
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return SlimHelperTrait
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

}
