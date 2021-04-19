<?php

namespace Enlightn\SecurityChecker\Tests;

use Enlightn\SecurityChecker\AdvisoryFetcher;
use Enlightn\SecurityChecker\Filesystem;
use Enlightn\SecurityChecker\ZipExtractor;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use PHPUnit\Framework\TestCase;

class ZipExtractorTest extends TestCase
{
    /**
     * @test
     */
    public function extracts_archive_with_zip_archiver()
    {
        if (! class_exists('ZipArchive')) {
            $this->markTestSkipped('The PHP zip extension is not installed.');
        }

        $zip = new ZipExtractor();

        $this->cleanExtractDirectory($this->getExtractDirectory());

        $zip->extractWithZipArchive($this->getArchivePath(), $extractPath = $this->getExtractDirectory());

        $this->assertTrue(is_dir($extractPath));
        $this->assertTrue(is_file($extractPath.DIRECTORY_SEPARATOR.$this->getSampleFileToValidate()));

        $this->cleanExtractDirectory($this->getExtractDirectory());
    }

    /**
     * @test
     */
    public function extracts_archive_with_unzip_command()
    {
        $zip = new ZipExtractor();

        if (! $zip->unzipCommandExists()) {
            $this->markTestSkipped('The unzip command is not installed.');
        }

        $this->cleanExtractDirectory($this->getExtractDirectory());

        $zip->extractWithSystemUnzip($this->getArchivePath(), $extractPath = $this->getExtractDirectory());

        $this->assertTrue(is_dir($extractPath));
        $this->assertTrue(is_file($extractPath.DIRECTORY_SEPARATOR.$this->getSampleFileToValidate()));

        $this->cleanExtractDirectory($this->getExtractDirectory());
    }

    protected function cleanExtractDirectory($extractPath)
    {
        (new Filesystem)->deleteDirectory($extractPath);
        $this->assertFalse(is_dir($extractPath));
    }

    protected function getExtractDirectory()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'extract';
    }

    protected function getArchivePath()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'laravel.zip';
    }

    protected function getSampleFileToValidate()
    {
        return 'laravel'.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'2014-04-15.yaml';
    }
}
