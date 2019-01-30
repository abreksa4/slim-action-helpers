<?php

namespace AndrewBreksa\SlimActionHelpers;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AbstractMiddleware
 * @package AndrewBreksa\BFrame\Web
 */
abstract class AbstractMiddleware implements ActionInterface
{
    use SlimHelperTrait;

    private $next;

    /**
     * @return mixed
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @param mixed $next
     * @return AbstractMiddleware
     */
    public function setNext($next)
    {
        $this->next = $next;
        return $this;
    }

    /**
     * Execute the middleware.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $this->bootstrap($request, $response);
        $this->setNext($next);
        $t_response = $this->act();
        if ($t_response instanceof ResponseInterface) {
            return $t_response;
        }
        return $next($request, $response);
    }

}
