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
 * Description of Project
 *
 * @author 郷
 */
class Project {

    private $rootPath;
    private $configFile;

    public function __construct($configFile) {
        if (is_string($configFile)) {
            $configFile = new \SplFileObject($configFile);
        }
        if ($configFile instanceof \SplFileObject) {
            $this->configFile = $configFile;
        } else {
            throw new \Chigi\Chiji\Exception\ConfigFileNotFoundException("The Config File Param given INVALID.");
        }
        $this->rootPath = dirname($this->configFile->getRealPath());
    }

    /**
     * Return the root path of current project
     * @return string
     */
    public function getRootPath() {
        return $this->rootPath;
    }

}
