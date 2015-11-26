<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Class supports caching information about state of fixing files.
 *
 * Cache is supported only for phar version and version installed via composer.
 *
 * File will be processed by PHP CS Fixer only if any of the following conditions is fulfilled:
 *  - cache is not available,
 *  - fixer version changed,
 *  - rules changed,
 *  - file is new,
 *  - file changed.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FileCacheManager
{
    private $cacheFile;
    private $isEnabled;
    private $rules;
    private $newHashes = array();
    private $oldHashes = array();

    /**
     * Create instance.
     *
     * @param bool   $isEnabled is cache enabled
     * @param string $cacheFile cache file
     * @param array  $rules     array defining rules, format like one for ConfigInterface::setRules
     */
    public function __construct($isEnabled, $cacheFile, array $rules)
    {
        $this->isEnabled = $isEnabled;
        $this->cacheFile = $cacheFile;
        $this->rules = $rules;

        $this->readFromFile();
    }

    public function __destruct()
    {
        $this->saveToFile();
    }

    public function needFixing($file, $fileContent)
    {
        if (!$this->isEnabled) {
            return true;
        }

        if (!isset($this->oldHashes[$file])) {
            return true;
        }

        if ($this->oldHashes[$file] !== $this->calcHash($fileContent)) {
            return true;
        }

        // file do not change - keep hash in new collection
        $this->newHashes[$file] = $this->oldHashes[$file];

        return false;
    }

    public function setFile($file, $fileContent)
    {
        if (!$this->isEnabled) {
            return;
        }

        $this->newHashes[$file] = $this->calcHash($fileContent);
    }

    private function calcHash($content)
    {
        return crc32($content);
    }

    private function isCacheStale($cacheVersion, $rules)
    {
        if (!$this->isEnabled) {
            return true;
        }

        return ToolInfo::getVersion() !== $cacheVersion || $this->rules !== $rules;
    }

    private function readFromFile()
    {
        if (!$this->isEnabled) {
            return;
        }

        if (!file_exists($this->cacheFile)) {
            return;
        }

        $content = file_get_contents($this->cacheFile);
        $data = unserialize($content);

        if (!isset($data['version']) || !isset($data['rules'])) {
            return;
        }

        // Set hashes only if the cache is fresh, otherwise we need to parse all files
        if (!$this->isCacheStale($data['version'], $data['rules'])) {
            $this->oldHashes = $data['hashes'];
        }
    }

    private function saveToFile()
    {
        if (!$this->isEnabled) {
            return;
        }

        $data = serialize(
            array(
                'version' => ToolInfo::getVersion(),
                'rules' => $this->rules,
                'hashes' => $this->newHashes,
            )
        );

        if (false === @file_put_contents($this->cacheFile, $data, LOCK_EX)) {
            throw new IOException(sprintf('Failed to write file "%s".', $this->cacheFile), 0, null, $this->cacheFile);
        }
    }
}
