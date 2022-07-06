<?php

namespace Enlightn\SecurityChecker\Tests\Journal;

use Http\Client\Common\Plugin\Journal;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class SimpleArray implements Journal
{
    public $successes = [];
    public $failures = [];

    public function addSuccess(RequestInterface $request, ResponseInterface $response)
    {
        $this->successes[] = [$request, $response];
    }

    public function addFailure(RequestInterface $request, ClientExceptionInterface $exception)
    {
        $this->failures[] = [$request, $exception];
    }
}
