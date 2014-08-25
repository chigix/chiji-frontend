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

/**
 * The Abstract class for project config.
 *
 * @author 郷
 */
abstract class ProjectConfig {

    private $projectRootPath = null;

    public function __construct() {
        
    }

    /**
     * Get the project root path defination
     * @return string
     */
    public function getProjectRootPath() {
        return $this->projectRootPath;
    }

    /**
     * Set the project root path defination
     * @param string $dir_path
     */
    public function setProjectRootPath($dir_path) {
        $this->projectRootPath = $dir_path;
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
        $road_map->append(new SourceRoad("ROOT", $this->getProjectRootPath()));
        return $road_map;
    }

}
