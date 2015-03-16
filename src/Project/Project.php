<?php

/*
 * Copyright 2014 郷.
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

namespace Chigi\Chiji\Project;

use Chigi\Chiji\Collection\RoadMap;
use Chigi\Chiji\Exception\ConfigFileNotFoundException;
use Chigi\Chiji\Exception\InvalidConfigException;
use Chigi\Chiji\File\AbstractResourceFile;
use Chigi\Component\IO\File;

/**
 * Description of Project
 *
 * @author 郷
 */
class Project {

    private $rootDir;
    private $configFile;
    private $projectName;

    /**
     *
     * @var RoadMap
     */
    private $roadMap;

    /**
     * The Resources.
     *
     * @var {md5($resources_path):AbstractResourceFile}
     */
    private $resources = array();
    
    /**
     *
     * @var \Chigi\Chiji\Collection\ResourcesCollection
     */
    private $releases = null;

    /**
     *
     * @var \Chigi\Chiji\Util\CacheManager
     */
    private $caches;

    /**
     * 
     * @param File $configFile
     * @throws ConfigFileNotFoundException
     * @throws InvalidConfigException
     */
    public function __construct($configFile) {
        if (is_string($configFile)) {
            $configFile = new File($configFile);
        }
        if ($configFile instanceof File) {
            $this->configFile = $configFile;
        } else {
            throw new ConfigFileNotFoundException("The Config File Param given INVALID.");
        }
        $this->rootDir = new File(dirname($this->configFile->getAbsolutePath()));
        $this->releases = new \Chigi\Chiji\Collection\ResourcesCollection();
        $project_config = require($this->configFile->getAbsolutePath());
        if ($project_config instanceof ProjectConfig) {
            $this->pushConfig($project_config);
        } else {
            throw new InvalidConfigException(sprintf("The return from config '%s' is not instance of ProjectConfig", $this->configFile->getAbsolutePath()));
        }
    }

    /**
     * Return the root path of current project
     * @return string
     */
    public function getRootPath() {
        return $this->rootDir->getAbsolutePath();
    }

    /**
     * Get the project Name.
     * @return string
     */
    public function getProjectName() {
        return $this->projectName;
    }

    /**
     * Make the target Config object Effective<br/>
     * Must be used in config file ONLY!!
     * @param ProjectConfig $config
     * @throws InvalidConfigException
     */
    private function pushConfig(ProjectConfig $config) {
        if (is_null($config->getProjectRootDir())) {
            $config->setProjectRootDir($this->rootDir);
        } else {
            $this->rootDir = $config->getProjectRootDir();
            if (!$this->rootDir->isDirectory()) {
                throw new InvalidConfigException(sprintf("The rootpath \"%s\" IS INVALID", $this->getRootPath()));
            }
        }
        $this->projectName = $config->getProjectName();
        $this->caches = new \Chigi\Chiji\Util\CacheManager($config->getCacheDir(), $this);
        $this->caches->registerDirectory($this->rootDir);
        $config->setCacheManager($this->caches);
        $this->roadMap = $config->getRoadMap();
        if (!$this->roadMap instanceof RoadMap) {
            throw new \Exception("[" . $config->getProjectName() . "] ERROR: The getRoadMap Configuration must return RoadMap instance.");
        }
        foreach ($this->roadMap as $road) {
            /* @var $road SourceRoad */
            if (strpos($road->getSourceDir()->getAbsolutePath(), $config->getCacheDir()->getAbsolutePath()) === FALSE) {
                $this->caches->registerDirectory($road->getSourceDir());
            };
        }
    }

    /**
     * Returns the first match road for the target resource file 
     * with auto registration for the matched resource file.
     * @param File $file
     * @return SourceRoad|null
     */
    public function getMatchRoad(File $file) {
        foreach ($this->roadMap as $road) {
            /* @var $road SourceRoad */
            if ($road->resourceCheck($file)) {
                return $road;
            }
        }
        return NULL;
    }

    /**
     * Returns all the source directory paths from roadmap.
     * @return array<string>
     */
    public function getSourceDirs() {
        $dirs = array();
        foreach ($this->roadMap as $road) {
            /* @var $road SourceRoad */
            array_push($dirs, $road->getSourceDir()->getAbsolutePath());
        }
        return $dirs;
    }

    /**
     * Returns all the release directory paths from roadmap.
     * @return array<string>
     */
    public function getReleaseDirs() {
        $dirs = array();
        foreach ($this->roadMap as $road) {
            /* @var $road SourceRoad */
            $release_dir = $road->getReleaseDir();
            if (empty($release_dir)) {
                continue;
            }
            if (!$release_dir->exists()) {
                if (!$release_dir->mkdirs()) {
                    continue;
                }
            }
            array_push($dirs, $release_dir->getAbsolutePath());
        }
        return $dirs;
    }

    /**
     * Get all the Member Elements such as SourceRoad, annotation and resources.
     * 
     * @return {memberId:MemberIdentifier}
     */
    public function getAllMembers() {
        $this->roadMap;
        $this->resources;
        $result = array();
        foreach ($this->roadMap as $road) {
            /* @var $road SourceRoad */
            $result[$road->getMemberId()] = $road;
        }
        foreach ($this->resources as $resource) {
            /* @var $resource AbstractResourceFile */
            $result[$resource->getMemberId()] = $resource;
        }
        return $result;
    }

    /**
     * Register a resource to this project.
     * 
     * @param AbstractResourceFile $resource
     */
    public function registerResource(AbstractResourceFile $resource) {
        if (!isset($this->resources[md5($resource->getRealPath())])) {
            $this->resources[md5($resource->getRealPath())] = $resource;
        }
    }

    /**
     * Get the target resource via the given file object or return null
     * if not registered.
     * 
     * @param File $file
     * @return AbstractResourceFile The corresponding resource object.
     */
    public function getResourceByFile(File $file) {
        if (isset($this->resources[md5($file->getAbsolutePath())])) {
            return $this->resources[md5($file->getAbsolutePath())];
        } else {
            return null;
        }
    }

    /**
     * Returns all the resource objects registered in this project currently.
     * 
     * @return array<AbstractResourceFile>
     */
    public function getRegisteredResources() {
        return array_values($this->resources);
    }

    /**
     * 
     * @return \Chigi\Chiji\Util\CacheManager
     */
    public function getCacheManager() {
        return $this->caches;
    }
    
    /**
     * Returns the resources to release as a collection.
     * 
     * @return \Chigi\Chiji\Collection\ResourcesCollection
     */
    public function getReleasesCollection() {
        return $this->releases;
    }
    
}
