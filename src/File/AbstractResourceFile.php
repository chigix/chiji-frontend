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
use Chigi\Component\IO\File;
use Chigi\Component\IO\FileSystem;

/**
 * The abstract class for resource file
 *
 * @author 郷
 */
class AbstractResourceFile {

    /**
     * The current resource file.
     * @var File
     */
    private $file;

    /**
     * 
     * @param File $file
     * @throws ResourceNotFoundException
     */
    public function __construct(File $file) {
        $this->file = $file;
        if (!$this->file->isFile()) {
            throw new ResourceNotFoundException("The Resource file '$file_path' NOT FOUND.");
        }
    }

    /**
     * Get the resource file.
     * @return File
     */
    public final function getFile() {
        return $this->file;
    }

    public function getRealPath() {
        return $this->file->getAbsolutePath();
    }

    /**
     * Returns the relative path
     * @param File $base_dir The base_dir to be based upon for relative path calculation
     * @return string the relative path
     */
    public function getRelativePath(File $base_dir) {
        return FileSystem::getFileSystem()->makePathRelative($this->file->getParent(), $base_dir->getAbsolutePath()) . $this->file->getName();
    }

    public function getId() {
        return md5($this->file->getAbsolutePath());
    }

    /**
     * Return the file contents
     * @return string
     */
    public final function getFileContents() {
        return file_get_contents($this->file->getAbsolutePath());
    }

    /**
     * Get the md5 hash of the current file contents.
     * @return string
     */
    public function getHash() {
        return md5_file($this->getFile()->getAbsolutePath());
    }

}
