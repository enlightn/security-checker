<?php

namespace Enlightn\SecurityChecker\Tests;

use Enlightn\SecurityChecker\AdvisoryFetcher;
use Enlightn\SecurityChecker\Filesystem;
use Enlightn\SecurityChecker\Tests\Journal\SimpleArray;
use Http\Client\Common\Plugin\HistoryPlugin;
use Http\Client\Common\PluginClient;
use PHPUnit\Framework\TestCase;

class AdvisoryFetcherTest extends TestCase
{
    /**
     * @test
     */
    public function fetches_archives_with_and_without_cache()
    {
        $fetcher = new AdvisoryFetcher;
        $journal = new SimpleArray;

        $pluginClient = new PluginClient(
            $fetcher->getClient(),
            [new HistoryPlugin($journal)]
        );

        $fetcher->setClient($pluginClient);

        // Test for non-cached version.
        $this->cleanCacheFiles($fetcher);
        $this->cleanExtractDirectory($fetcher);

        $fetcher->fetchAdvisories();
        $this->assertTrue(is_dir($fetcher->getExtractDirectoryPath()));
        $this->assertTrue(is_file($fetcher->getCacheFilePath()));
        $this->assertTrue(is_file($fetcher->getExtractDirectoryPath().DIRECTORY_SEPARATOR.$this->getSampleFileToValidate()));
        $this->assertCount(1, $journal->successes);
        $this->assertSame(200, $journal->successes[0][1]->getStatusCode());

        // Test for cached version.
        $this->cleanExtractDirectory($fetcher);

        $fetcher->fetchAdvisories();
        $this->assertTrue(is_dir($fetcher->getExtractDirectoryPath()));
        $this->assertTrue(is_file($fetcher->getCacheFilePath()));
        $this->assertTrue(is_file($fetcher->getExtractDirectoryPath().DIRECTORY_SEPARATOR.$this->getSampleFileToValidate()));
        $this->assertCount(2, $journal->successes);
        $this->assertSame(304, $journal->successes[1][1]->getStatusCode());;

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

    protected function getSampleFileToValidate()
    {
        return 'security-advisories-master'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR.'framework'
            .DIRECTORY_SEPARATOR.'2014-04-15.yaml';
    }
}
