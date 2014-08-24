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

use Chigi\Chiji\Exception\ConfigFileNotFoundException;
use Chigi\Chiji\Exception\ConflictProjectNameException;
use Chigi\Chiji\Exception\InvalidConfigException;
use Chigi\Chiji\Exception\ProjectNotFoundException;
use SplFileObject;

/**
 * Description of Project
 *
 * @author 郷
 */
class Project {

    private $rootPath;
    private $configFile;
    private $projectName;
    private $constants;
    private $releasePathFormat = "";
    private $releaseUrlFormat = "";

    /**
     *
     * @var array<Project>
     */
    private static $instances = array();

    /**
     *
     * @var Project
     */
    private static $currentProject = null;

    /**
     * 
     * @param SplFileObject $configFile
     * @throws ConfigFileNotFoundException
     */
    public function __construct($configFile) {
        if (is_string($configFile)) {
            $configFile = new SplFileObject($configFile);
        }
        if ($configFile instanceof SplFileObject) {
            $this->configFile = $configFile;
        } else {
            throw new ConfigFileNotFoundException("The Config File Param given INVALID.");
        }
        $this->rootPath = dirname($this->configFile->getRealPath());
        $tmp_current_project = self::$currentProject;
        self::$currentProject = $this;
        require_once $this->configFile->getRealPath();
        self::$currentProject = $tmp_current_project;
    }

    /**
     * Return the root path of current project
     * @return string
     */
    public function getRootPath() {
        return $this->rootPath;
    }

    /**
     * Regist a project object as static
     * @param Project $project
     * @param boolean $fresh_current Is to be set as current?
     * @throws ConflictProjectNameException
     */
    public static function registProject(Project $project, $fresh_current = FALSE) {
        if (isset(self::$instances[$project->getProjectName()])) {
            throw new ConflictProjectNameException(sprintf("The project \"%s\" EXISTS.", $project->getProjectName()));
        }
        self::$instances[$project->getProjectName()] = $project;
        if ($fresh_current) {
            self::$currentProject = $project;
        }
    }

    /**
     * Get the target project registered.
     * @param string $project_name
     * @return Project
     * @throws ProjectNotFoundException
     */
    public static function getRegistered($project_name = null) {
        if (empty($project_name)) {
            if (is_null(self::$currentProject)) {
                throw new ProjectNotFoundException("NONE project registered as current.");
            } else {
                return self::$currentProject;
            }
        } else {
            if (isset(self::$instances[$project_name])) {
                return self::$instances[$project_name];
            } else {
                throw new ProjectNotFoundException(sprintf("The project \"%s\" NOT registered.", $project_name));
            }
        }
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
     * @param \Chigi\Chiji\Project\ProjectConfig $config
     * @throws InvalidConfigException
     */
    public static function pushConfig(ProjectConfig $config) {
        if (!is_null($config->getProjectRootPath())) {
            self::$currentProject->rootPath = $config->getProjectRootPath();
            if (!is_dir(self::$currentProject->rootPath)) {
                throw new InvalidConfigException(sprintf("The rootpath \"%s\" IS INVALID", $this->rootPath));
            }
        }
        self::$currentProject->constants['ROOT'] = self::$currentProject->rootPath;
        self::$currentProject->projectName = $config->getProjectName();
        self::$currentProject->releasePathFormat = $config->getReleaseRootPath();
        if (strpos(self::$currentProject->releasePathFormat, '%s') === FALSE) {
            throw new \Exception("The getReleaseRootPath result is not compatible for sprintf.");
        }
        self::$currentProject->releaseUrlFormat = $config->getReleaseRootUrl();
        if (strpos(self::$currentProject->releaseUrlFormat, '%s') === FALSE) {
            throw new \Exception("The getReleaseRootUrl result is not compatible for sprintf.");
        }
    }

    /**
     * The destination to release<br/>
     * Please use with sprintf
     * @return string The sprintf string format
     */
    public function getReleaseRootPathFormat() {
        return $this->releasePathFormat;
    }

    /**
     * The URL accessed to the Release Root Path<br/>
     * Please use with sprintf
     * @return string The sprintf string format
     */
    public function getReleaseRootUrlFormat() {
        return $this->releaseUrlFormat;
    }

}
