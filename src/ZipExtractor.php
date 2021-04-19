<?php

namespace Enlightn\SecurityChecker;

use RuntimeException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use UnexpectedValueException;
use ZipArchive;

class ZipExtractor
{
    /**
     * @param string $archivePath
     * @param string $extractPath
     */
    public function extract($archivePath, $extractPath)
    {
        if ($this->unzipCommandExists()) {
            $this->extractWithSystemUnzip($archivePath, $extractPath);

            return;
        }

        if (class_exists('ZipArchive')) {
            $this->extractWithZipArchive($archivePath, $extractPath);

            return;
        }

        throw new RuntimeException('The unzip command and zip php extension are both missing.');
    }

    /**
     * @return bool
     */
    public function unzipCommandExists()
    {
        $finder = new ExecutableFinder;

        return (bool) $finder->find('unzip');
    }

    /**
     * @param string $archivePath
     * @param string $extractPath
     */
    public function extractWithSystemUnzip($archivePath, $extractPath)
    {
        $process = new Process(['unzip', '-qq', '-o', $archivePath, '-d', $extractPath]);

        $process->mustRun();
    }

    /**
     * @param string $archivePath
     * @param string $extractPath
     */
    public function extractWithZipArchive($archivePath, $extractPath)
    {
        $zip = new ZipArchive;
        $openResult = $zip->open($archivePath);

        if (true === $openResult) {
            $extractResult = $zip->extractTo($extractPath);

            if (true === $extractResult) {
                $zip->close();
            } else {
                throw new RuntimeException('There was an error in extracting the ZIP file. It is either corrupted or using an invalid format.');
            }
        } else {
            throw new UnexpectedValueException($this->getErrorMessage($openResult, $archivePath));
        }
    }

    /**
     * Give a meaningful error message to the user.
     *
     * @param  int    $retval
     * @param  string $file
     * @return string
     */
    protected function getErrorMessage($retval, $file)
    {
        switch ($retval) {
            case ZipArchive::ER_EXISTS:
                return sprintf("File '%s' already exists.", $file);
            case ZipArchive::ER_INCONS:
                return sprintf("Zip archive '%s' is inconsistent.", $file);
            case ZipArchive::ER_INVAL:
                return sprintf("Invalid argument (%s)", $file);
            case ZipArchive::ER_MEMORY:
                return sprintf("Malloc failure (%s)", $file);
            case ZipArchive::ER_NOENT:
                return sprintf("No such zip file: '%s'", $file);
            case ZipArchive::ER_NOZIP:
                return sprintf("'%s' is not a zip archive.", $file);
            case ZipArchive::ER_OPEN:
                return sprintf("Can't open zip file: %s", $file);
            case ZipArchive::ER_READ:
                return sprintf("Zip read error (%s)", $file);
            case ZipArchive::ER_SEEK:
                return sprintf("Zip seek error (%s)", $file);
            default:
                return sprintf("'%s' is not a valid zip archive, got error code: %s", $file, $retval);
        }
    }
}
