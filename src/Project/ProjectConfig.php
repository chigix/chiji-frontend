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

use Chigi\Chiji\Collection\ExtensionCollection;
use Chigi\Chiji\Collection\RoadMap;
use Chigi\Chiji\Util\CacheManager;
use Chigi\Component\IO\File;

/**
 * The Abstract class for project config.
 *
 * @author 郷
 */
abstract class ProjectConfig {

    /**
     *
     * @var File
     */
    private $projectRootDir = null;

    public function __construct() {
        
    }

    /**
     * @todo TO REMOVE
     * Get the project root path defination
     * @return string
     */
    public function getProjectRootPath() {
        return $this->projectRootDir->getAbsolutePath();
    }

    /**
     * 
     * @return File
     */
    public function getProjectRootDir() {
        return $this->projectRootDir;
    }

    /**
     * Set the project root directory defination.
     * 
     * @param File $root_dir
     */
    public final function setProjectRootDir(File $root_dir) {
        $this->projectRootDir = $root_dir;
        return $this;
    }

    /**
     * @return string Specify the project name
     */
    abstract public function getProjectName();

    /**
     * Returns the roadmap for this project.
     * @return RoadMap
     */
    public function getRoadMap() {
        $road_map = new RoadMap();
        $road_map->append(new LessRoad("LESSCSS", $this->generateCacheDir($this->getProjectRootDir())));
        $road_map->append(new SourceRoad("ROOT", $this->generateCacheDir($this->getProjectRootDir())));
        $road_map->append(new BuildRoad("BuildCache", $this->getProjectRootDir()));
        return $road_map;
    }

    /**
     * The Cache Directory for project building.
     *
     * @var File
     */
    private $__project_cache_dir = null;

    /**
     * Set the Cache directory for this project.
     * 
     * @return File
     */
    public function getCacheDir() {
        if (is_null($this->__project_cache_dir)) {
            $this->__project_cache_dir = new File("." . uniqid(), $this->projectRootDir->getAbsolutePath());
        }
        return $this->__project_cache_dir;
    }

    /**
     *
     * @var CacheManager
     */
    private $__mock_cache_manager = null;

    /**
     * 
     * @param File $rcDir
     * @return File
     */
    protected final function generateCacheDir(File $rcDir) {
        $this->__mock_cache_manager->registerDirectory($rcDir);
        return $this->__mock_cache_manager->searchCacheDir($rcDir);
    }

    public final function setCacheManager(CacheManager $cache) {
        $this->__mock_cache_manager = $cache;
    }

}
