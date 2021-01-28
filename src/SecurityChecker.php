<?php

namespace Enlightn\SecurityChecker;

class SecurityChecker
{
    /**
     * @param string $composerLockPath
     * @param false $excludeDev
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function check(string $composerLockPath, $excludeDev = false): array
    {
        $parser = new AdvisoryParser((new AdvisoryFetcher)->fetchAdvisories());

        $dependencies = (new Composer)->getDependencies($composerLockPath, $excludeDev);

        return (new AdvisoryAnalyzer($parser->getAdvisories()))->analyzeDependencies($dependencies);
    }
}
