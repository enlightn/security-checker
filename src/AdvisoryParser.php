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

    public function __construct($advisoriesDirectory)
    {
        $this->advisoriesDirectory = $advisoriesDirectory;
    }

    public function getAdvisories(array $allowList = [])
    {
        $files = (new Finder)->in($this->advisoriesDirectory)->files()->name('*.yaml');

        foreach ($files as $fileInfo) {
            $contents = Yaml::parseFile($fileInfo->getRealPath());

            if (isset($contents['cve']) && in_array($contents['cve'], $allowList, true)) {
                continue;
            }

            if (isset($contents['title']) && in_array($contents['title'], $allowList, true)) {
                continue;
            }

            $package = str_replace('composer://', '', $contents['reference']);

            unset($contents['reference']);

            $this->advisories[$package][] = $contents;
        }

        return $this->advisories;
    }
}
