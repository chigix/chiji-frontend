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

namespace Chigi\Chiji\File;

use Chigi\Chiji\Exception\ResourceNotFoundException;
use Chigi\Chiji\Util\PathHelper;

/**
 * The abstract class for resource file
 *
 * @author 郷
 */
class AbstractResourceFile {

    /**
     * The real path of the current resource file.
     * @var string
     */
    private $file_realpath;

    /**
     * 
     * @param string $file_path
     * @throws ResourceNotFoundException
     */
    public function __construct($file_path) {
        $this->file_realpath = realpath($file_path);
        if ($this->file_realpath === FALSE) {
            throw new ResourceNotFoundException("The Resource file '$file_path' NOT FOUND.");
        }
    }

    /**
     * Get the file real path.
     * @return string
     */
    public final function getRealPath() {
        return PathHelper::pathStandardize($this->file_realpath);
    }

    /**
     * Returns the relative path
     * @param string $base_dir The base_dir to be based upon for relative path calculation
     * @return string the relative path
     */
    public function getRelativePath($base_dir) {
        $back_count = 0;
        while (strpos($this->getRealPath(), $base_dir) !== 0 && !empty($base_dir)) {
            $base_dir = dirname($base_dir);
            $back_count ++;
        }
        $return_path = substr($this->getRealPath(), strlen($base_dir) + 1);
        for ($i = 0; $i < $back_count; $i++) {
            $return_path = '../' . $return_path;
        }
        return $return_path;
    }

    public function getId() {
        return md5($this->file_realpath);
    }

    /**
     * Return the file contents
     * @return string
     */
    public final function getFileContents() {
        return file_get_contents($this->file_realpath);
    }

    /**
     * Get the md5 hash of the current file contents.
     * @return string
     */
    public function getHash() {
        return md5_file($this->getRealPath());
    }

}
