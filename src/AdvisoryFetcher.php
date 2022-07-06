<?php

namespace Enlightn\SecurityChecker;

use Http\Client\HttpClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\ResponseInterface;
use Http\Discovery\HttpClientDiscovery;

class AdvisoryFetcher
{
    /**
     * @var \Http\Client\HttpClient
     */
    private $client;

    /**
     * @var \Psr\Http\Message\RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var string
     */
    private $tempDir;

    const ADVISORIES_URL = 'https://codeload.github.com/FriendsOfPHP/security-advisories/zip/master';

    const CACHE_FILE_NAME = 'php_security_advisories.json';

    const ARCHIVE_FILE_NAME = 'php_security_advisories.zip';

    const EXTRACT_PATH = 'php_security_advisories';


    public function __construct($tempDir = null)
    {
        $this->client = HttpClientDiscovery::find();
        $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $this->tempDir = is_null($tempDir) ? sys_get_temp_dir() : $tempDir;
    }

    public function fetchAdvisories()
    {
        $archivePath = $this->fetchAdvisoriesArchive();

        (new Filesystem)->deleteDirectory($extractPath = $this->getExtractDirectoryPath());

        $zip = new ZipExtractor;
        $zip->extract($archivePath, $extractPath);

        return $extractPath;
    }

    /**
     * @return string
     */
    public function fetchAdvisoriesArchive()
    {
        $request = $this->requestFactory->createRequest('GET', self::ADVISORIES_URL);
        $cacheResult = [];

        if (! empty($cache = $this->getCacheFile())) {
            $cacheResult = json_decode($cache, true);
            if (is_file($cacheResult['ArchivePath'])) {
                // Set cache headers only if both the cache file and archive file exist.
                $request = $request->withHeader('If-None-Match', $cacheResult['Key']);
                $request = $request->withHeader('If-Modified-Since', $cacheResult['Date']);
            }
        }

        $response = $this->client->sendRequest($request);

        if ($response->getStatusCode() !== 304) {
            $this->writeCacheFile($response);

            $this->storeAdvisoriesArchive((string) $response->getBody());

            return $this->getArchiveFilePath();
        }

        // If a 304 Not Modified header is found, simply rely on the cached archive file.
        return $cacheResult['ArchivePath'];
    }

    /**
     * @param string $content
     * @return false|int
     */
    public function storeAdvisoriesArchive($content)
    {
        return file_put_contents($this->getArchiveFilePath(), $content);
    }

    /**
     * @return false|string|null
     */
    public function getCacheFile()
    {
        if (! is_file($path = $this->getCacheFilePath())) {
            return null;
        }

        return file_get_contents($path);
    }

    public function getCacheFilePath()
    {
        return $this->tempDir.DIRECTORY_SEPARATOR.self::CACHE_FILE_NAME;
    }

    public function getArchiveFilePath()
    {
        return $this->tempDir.DIRECTORY_SEPARATOR.self::ARCHIVE_FILE_NAME;
    }

    public function getExtractDirectoryPath()
    {
        return $this->tempDir.DIRECTORY_SEPARATOR.self::EXTRACT_PATH;
    }

    public function setClient(HttpClient $client)
    {
        $this->client = $client;
    }

    protected function writeCacheFile(ResponseInterface $response)
    {
        $cache = [
            'Key' => $response->getHeader('Etag')[0],
            'Date' => $response->getHeader('Date')[0],
            'ArchivePath' => $this->getArchiveFilePath(),
        ];

        file_put_contents($this->getCacheFilePath(), json_encode($cache), LOCK_EX);
    }

    /**
     * @return HttpClient
     */
    public function getClient()
    {
        return $this->client;
    }
}
