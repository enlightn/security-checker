<?php

namespace Enlightn\SecurityChecker\Tests;

use Enlightn\SecurityChecker\Filesystem;
use Enlightn\SecurityChecker\ZipExtractor;
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

    /**
     * @test
     */
    public function can_i_chose_extracting_with_zip_commend()
    {
        $extractorMock = $this->getMockBuilder(ZipExtractor::class)
            ->setMethods(['extractWithSystemUnzip'])
            ->getMock();

        $extractorMock->expects($this->once())
            ->method('extractWithSystemUnzip')
            ->with($this->identicalTo("arquive/path", "extract/path"));

        $extractorMock->extract(
            "arquive/path",
            "extract/path",
            "system-unzip"
        );
    }

    /**
     * @test
     */
    public function can_i_chose_extracting_with_zip_extension()
    {
        $extractorMock = $this->getMockBuilder(ZipExtractor::class)
            ->setMethods(['extractWithZipArchive'])
            ->getMock();

        $extractorMock->expects($this->once())
            ->method('extractWithZipArchive')
            ->with($this->identicalTo("arquive/path", "extract/path"));

        $extractorMock->extract(
            "arquive/path",
            "extract/path",
            "zip-extension"
        );
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
