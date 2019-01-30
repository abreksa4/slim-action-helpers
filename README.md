slim-action-helpers
-------------------

![build-status](https://travis-ci.org/abreksa4/slim-action-helpers.svg?branch=master) 
![license](https://img.shields.io/github/license/abreksa4/slim-action-helpers.svg) 
![stars](https://img.shields.io/github/stars/abreksa4/slim-action-helpers.svg)
![Coverage Status](https://coveralls.io/repos/github/abreksa4/slim-action-helpers/badge.svg?branch=master)

Quick and dirty helper classes for RAD slim 3 development that fully support the `__invoke` Slim 3 middleware and 
callable paradigm. 

# Examples

## Action

```php
<?php

namespace AndrewBreksa\SlimActionHelpers\Example\Actions;

use AndrewBreksa\SlimActionHelpers\AbstractAction;

class ExampleAction extends AbstractAction
{

    public function act()
    {
        // do some magic here
        return $this->json([
            'entity' => [
                'email' => 'andrew@andrewbreksa.com',
            ]
        ], 201);
    }
}

$app->post('/emails', \AndrewBreksa\SlimActionHelpers\Example\Actions\ExampleAction::class);

```

## Middleware
```php
<?php

namespace AndrewBreksa\SlimActionHelpers\Example\Middleware;

use AndrewBreksa\SlimActionHelpers\AbstractMiddleware;
use Psr\Log\LoggerInterface;

class RequestLoggingMiddleware extends AbstractMiddleware
{
    /**
     * Here, if a ResponseInterface is returned, the stack is ejected from, otherwise we continue on and automaically
     * call $next
     * @return mixed|void|null
     */
    public function act()
    {
        $this->getContainer()->get(LoggerInterface::class)->debug('request', [
            'method' => $this->getRequest()->getMethod(),
            'uri' => $this->getRequest()->getUri()->getPath(),
            'query' => $this->getRequest()->getQueryParams(),
            'headers' => $this->getRequest()->getHeaders()
        ]);
    }

}

$app->add(\AndrewBreksa\SlimActionHelpers\Example\Middleware\RequestLoggingMiddleware::class);

```

# Docs
Eventually I may add very detailed docs, until then read the very simple source. It's literally 4 files. 