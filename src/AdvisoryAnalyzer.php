<?php

namespace Enlightn\SecurityChecker;

class AdvisoryAnalyzer
{
    /**
     * @var array
     */
    private $advisories;

    public function __construct(array $advisories)
    {
        $this->advisories = $advisories;
    }

    /**
     * Returns an array of vulnerabilities for the given package and version.
     *
     * @param string $package
     * @param string $version
     * @param string|null|int $time
     * @return array
     */
    public function analyzeDependency(string $package, string $version, $time = null): array
    {
        if (! isset($this->advisories[$package])) {
            return [];
        }

        $vulnerabilities = [];

        if (is_string($time)) {
            $time = strtotime($time) ?: null;
        }

        foreach ($this->advisories[$package] as $advisory ) {
            $packageBranchName = $this->normalizeVersion($version);

            foreach ($advisory['branches'] as $branch => $versionInfo) {
                if ($this->isDevPackage($version)) {
                    $branchName = preg_replace('/.x$', '', $branch);

                    if ($branchName !== $packageBranchName) {
                        continue;
                    }

                    if (is_null($time) || is_null($versionInfo['time']) || $time > $versionInfo['time']) {
                        continue;
                    }
                } else {
                    $passed = false;

                    foreach ($versionInfo['versions'] as $versionConstraint) {
                        $constrainedVersion = str_replace(['>', '<', '='], '', $versionConstraint);
                        $operator = str_replace($constrainedVersion, '', $versionConstraint) ?: '=';

                        if (version_compare($version, $constrainedVersion, $operator)) {
                            continue;
                        } else {
                            $passed = true;
                            break;
                        }
                    }

                    if ($passed) {
                        continue;
                    }
                }

                $vulnerabilities[] = [
                    'title' => $advisory['title'] ?? null,
                    'link' => $advisory['link'] ?? null,
                    'cve' => $advisory['cve'] ?? null,
                ];
            }
        }

        return $vulnerabilities;
    }

    public function analyzeDependencies(array $dependencies): array
    {
        $vulnerabilities = [];

        foreach ($dependencies as $package => $versionInfo) {
            $advisories = $this->analyzeDependency($package, $versionInfo['version'], $versionInfo['time']);

            if (! empty($advisories)) {
                $vulnerabilities[$package] = [
                    'version' => $versionInfo['version'],
                    'time' => $versionInfo['time'],
                    'advisories' => $advisories,
                ];
            }
        }

        return $vulnerabilities;
    }

    protected function normalizeVersion(string $version): string
    {
        return preg_replace(['/-dev$/', '/^dev-/'], '', $version);
    }

    protected function isDevPackage(string $version): string
    {
        return ! is_null(preg_filter(['/-dev$/', '/^dev-/'], '', $version));
    }
}
