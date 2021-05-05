<?php

namespace Enlightn\SecurityChecker;

class SecurityChecker
{
    /**
     * @var string
     */
    private $tempDir;

    public function __construct($tempDir = null)
    {
        $this->tempDir = $tempDir;
    }

    /**
     * @param string $composerLockPath
     * @param false $excludeDev
     * @param array $allowList
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function check($composerLockPath, $excludeDev = false, $allowList = [])
    {
        $parser = new AdvisoryParser((new AdvisoryFetcher($this->tempDir))->fetchAdvisories());

        $dependencies = (new Composer)->getDependencies($composerLockPath, $excludeDev);

        return (new AdvisoryAnalyzer($parser->getAdvisories($allowList)))->analyzeDependencies($dependencies);
    }
}
