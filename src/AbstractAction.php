<?php
namespace AndrewBreksa\SlimActionHelpers;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AbstractAction
 * @package AndrewBreksa\BFrame\Web
 */
abstract class AbstractAction implements ActionInterface
{
    use SlimHelperTrait;

    /**
     * @var array
     */
    protected $args = [];

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return mixed|ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $args = [])
    {
        $this->bootstrap($request, $response);
        $this->args = $args;
        $response = $this->act();

        return $response;
    }

}
