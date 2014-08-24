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

/**
 * The Abstract class for project config.
 *
 * @author 郷
 */
abstract class ProjectConfig {

    public function __construct() {
        
    }

    /**
     * Get the project root path defination
     * @return string
     */
    public function getProjectRootPath() {
        return null;
    }

    /**
     * Return the sprintf format string
     * @return string The destination to release
     */
    abstract public function getReleaseRootPath();

    /**
     * Return the sprintf format string
     * @return string The URL accessed to the Release Root Path
     */
    abstract public function getReleaseRootUrl();

    /**
     * @return string Specify the project name
     */
    abstract public function getProjectName();
}
