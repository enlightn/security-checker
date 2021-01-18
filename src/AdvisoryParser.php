<?php

namespace Enlightn\SecurityChecker;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class AdvisoryParser
{
    /**
     * @var string
     */
    private $advisoriesDirectory;

    private $advisories = [];

    public function __construct(string $advisoriesDirectory)
    {
        $this->advisoriesDirectory = $advisoriesDirectory;
    }

    public function getAdvisories(): array
    {
        $files = (new Finder)->in($this->advisoriesDirectory)->files()->name('*.yaml');

        foreach ($files as $fileInfo) {
            $contents = Yaml::parseFile($fileInfo->getRealPath());

            $package = str_replace('composer://', '', $contents['reference']);

            unset($contents['reference']);

            $this->advisories[$package][] = $contents;
        }

        return $this->advisories;
    }
}