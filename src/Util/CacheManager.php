<?php

/*
 * Copyright 2015 Richard Lea <chigix@zoho.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Chigi\Chiji\Util;

use Chigi\Chiji\Exception\CacheLoopException;
use Chigi\Chiji\File\AbstractResourceFile;
use Chigi\Chiji\Project\Project;
use Chigi\Component\IO\File;
use Chigi\Component\IO\FileSystem;

/**
 * Description of CacheManager
 *
 * @author Richard Lea <chigix@zoho.com>
 */
class CacheManager {

    /**
     *
     * @var File
     */
    private $cacheDir;

    /**
     *
     * @var Project
     */
    private $parentProject;

    /**
     *
     * @var array{"resourceMemberId":File} resourceMemberId:CacheFileObj
     */
    private $cacheMap;

    /**
     *
     * @var array() {md5("origDirPath"):[File(origDir),File(cacheDir)]}
     */
    private $autoDirPathMap;

    /**
     * Used to replace invalid character in path string for cache path generation.
     *
     * @var string
     */
    private $autoReplaceSequence;

    /**
     * 
     * @param File $cacheDir
     * @param Project $project
     */
    function __construct(File $cacheDir, Project $project) {
        $this->cacheDir = $cacheDir;
        // NO CHECK FOR Project object because of the mock in ProjectConfig.
        $this->parentProject = $project;
        $this->cacheMap = array();
        $this->autoReplaceSequence = "[" . uniqid() . "]";
    }

    /**
     * 
     * @param AbstractResourceFile $resource
     * @param File $cache
     * @return CacheManager
     */
    public function registerCache(AbstractResourceFile $resource, File $cache) {
        $this->cacheMap[$resource->getMemberId()] = $cache;
        return $this;
    }

    /**
     * Returns the registered cache file built from the given $resource.
     * 
     * @param AbstractResourceFile $resource
     * @return File
     */
    public function getCacheBuilt(AbstractResourceFile $resource) {
        if (isset($this->cacheMap[$resource->getMemberId()])) {
            return $this->cacheMap[$resource->getMemberId()];
        } else {
            return null;
        }
    }

    public function openCache() {
        if ($this->cacheDir->exists()) {
            throw new \Exception("The Cache Dir Set Failed: " . $this->cacheDir->getAbsolutePath());
        }
        $this->cacheDir->mkdirs();
    }

    public function closeCache() {
        FileSystem::getFileSystem()->remove($this->cacheDir->getAbsolutePath());
    }

    /**
     * 
     * @param File $origDir
     * @return CacheManager
     * @throws CacheLoopException
     */
    public function registerDirectory(File $origDir) {
        if (strpos($origDir->getAbsolutePath(), $this->cacheDir->getAbsolutePath()) !== FALSE) {
            throw new CacheLoopException;
        }
        if (isset($this->autoDirPathMap[md5($origDir->getAbsolutePath())])) {
            return $this;
        }
        $this->autoDirPathMap[md5($origDir->getAbsolutePath())] = array(
            $origDir, new File(
                    str_replace(
                            array('|', ':', '*', '?', '"', '<', '>')
                            , $this->autoReplaceSequence
                            , $origDir->getAbsolutePath()
                    ), $this->cacheDir->getAbsolutePath() . "/." . uniqid()
            )
        );
        return $this;
    }

    /**
     * Search the cache directory by mapping original directory
     * 
     * @param File $origDir
     * @return File
     */
    public function searchCacheDir(File $origDir) {
        if (isset($this->autoDirPathMap[\md5($origDir->getAbsolutePath())])) {
            $entry = $this->autoDirPathMap[\md5($origDir->getAbsolutePath())];
            return $entry[1];
        } else {
            return null;
        }
    }

    /**
     * Search the original directory by mapping cached directory
     * 
     * @param File $cacheDir
     * @return File
     */
    public function searchOrigDir(File $cacheDir) {
        foreach ($this->autoDirPathMap as $entry_set) {
            $orig_dir = $entry_set[0];
            $cache_dir = $entry_set[1];
            if ($cacheDir->getAbsolutePath() === $cache_dir->getAbsolutePath()) {
                return $orig_dir;
            }
        }
        return null;
    }

}
