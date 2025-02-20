<?php

declare(strict_types=1);

use HttpSoft\Message\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

return [
    ServerRequestInterface::class => ServerRequest::class,
];