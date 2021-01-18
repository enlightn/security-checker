<?php

namespace Enlightn\SecurityChecker\Tests;

use Enlightn\SecurityChecker\AdvisoryFetcher;
use Enlightn\SecurityChecker\Filesystem;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use PHPUnit\Framework\TestCase;

class AdvisoryFetcherTest extends TestCase
{
    /**
     * @test
     */
    public function fetches_archives_with_and_without_cache()
    {
        $fetcher = new AdvisoryFetcher;
        $container = [];
        $fetcher->setClient(new Client([
            'handler' => $this->setupHistoryMiddleware($container),
        ]));

        // Test for non-cached version.
        $this->cleanCacheFiles($fetcher);
        $this->cleanExtractDirectory($fetcher);

        $fetcher->fetchAdvisories();
        $this->assertTrue(is_dir($fetcher->getExtractDirectoryPath()));
        $this->assertTrue(is_file($fetcher->getCacheFilePath()));
        $this->assertTrue(is_file($fetcher->getExtractDirectoryPath().DIRECTORY_SEPARATOR.$this->getSampleFileToValidate()));
        $this->assertCount(1, $container);
        $this->assertSame(200, $container[0]['response']->getStatusCode());

        // Test for cached version.
        $this->cleanExtractDirectory($fetcher);

        $fetcher->fetchAdvisories();
        $this->assertTrue(is_dir($fetcher->getExtractDirectoryPath()));
        $this->assertTrue(is_file($fetcher->getCacheFilePath()));
        $this->assertTrue(is_file($fetcher->getExtractDirectoryPath().DIRECTORY_SEPARATOR.$this->getSampleFileToValidate()));
        $this->assertCount(2, $container);
        $this->assertSame(304, $container[1]['response']->getStatusCode());

        $this->cleanCacheFiles($fetcher);
    }

    protected function cleanExtractDirectory(AdvisoryFetcher $fetcher)
    {
        (new Filesystem)->deleteDirectory($fetcher->getExtractDirectoryPath());
        $this->assertFalse(is_dir($fetcher->getExtractDirectoryPath()));
    }

    protected function cleanCacheFiles(AdvisoryFetcher $fetcher)
    {
        @unlink($fetcher->getCacheFilePath());
        @unlink($fetcher->getArchiveFilePath());
    }

    protected function setupHistoryMiddleware(&$container)
    {
        $handlerStack = HandlerStack::create();
        $handlerStack->push(Middleware::history($container));

        return $handlerStack;
    }

    protected function getSampleFileToValidate()
    {
        return 'security-advisories-master'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR.'framework'
            .DIRECTORY_SEPARATOR.'2014-04-15.yaml';
    }
}
