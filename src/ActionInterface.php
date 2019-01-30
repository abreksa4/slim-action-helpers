<?php
namespace AndrewBreksa\SlimActionHelpers;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ActionInterface
 * @package AndrewBreksa\BFrame\Web
 */
interface ActionInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return mixed|null
     */
    public function act();
}
