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

use Chigi\Chiji\Collection\ResourcesCollection;
use Chigi\Chiji\Exception\FileWriteErrorException;
use Chigi\Chiji\Exception\ProjectMemberNotFoundException;
use Chigi\Chiji\Exception\UndefinedReleaseUrlFormatException;
use Chigi\Chiji\File\AbstractResourceFile;
use Chigi\Chiji\File\CssResourceFile;
use Chigi\Chiji\File\GifResourceFile;
use Chigi\Chiji\File\JpegResourceFile;
use Chigi\Chiji\File\JsResourceFile;
use Chigi\Chiji\File\LessResourceFile;
use Chigi\Chiji\File\PlainResourceFile;
use Chigi\Chiji\File\PngResourceFile;
use Chigi\Chiji\Util\ProjectUtil;
use Chigi\Component\IO\File;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * Config for a release road for ONE RESOURCE. 
 * Generally only one type of resource road should be included in a Source Roadmap.
 *
 * @author 郷
 */
class SourceRoad implements MemberIdentifier {

    /**
     *
     * @var File
     */
    private $sourceDir;
    private $name;
    private $requires;
    private $useHash;
    private $charset;

    /**
     *
     * @var File
     */
    private $releaseDir;

    /**
     * 
     * @param string $name The roadname for the directory
     * @param File $source_dir The target directory, support absolute Path ONLY.
     * @param File $release_dir The release directory, support absolute Path ONLY.
     */
    function __construct($name, File $source_dir, File $release_dir = NULL) {
        $this->sourceDir = $source_dir;
        $this->releaseDir = $release_dir;
        $name = trim($name);
        if (empty($name)) {
            throw new InvalidArgumentException(sprintf("The Roadname '%s' IS INVALID", $name));
        }
        $this->name = $name;
        $this->requires = new ResourcesCollection();
        $this->useHash = FALSE;
        $this->charset = "utf-8";
    }

    /**
     * Returns the source directory
     * @return File
     */
    public final function getSourceDir() {
        return $this->sourceDir;
    }

    /**
     * Check if the resource match this road<br/>
     * if true then the resource could be get from manager.
     * @param File $file The resource file path to check, MUST BE ABSOLUTE PATH
     * @return boolean Returns true if the target path match the road
     */
    public final function resourceCheck(File $file) {
        if ($this->resourcePathMatch($file)) {
            if (is_null($this->getParentProject()->getResourceByFile($file)) && !is_null($resource = $this->resourceFactory($file))) {
                $this->getParentProject()->registerResource($resource);
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Check if the resource match this road
     * @param File $file
     * @return boolean
     */
    protected function resourcePathMatch(File $file) {
        $source_dirpath = str_replace('#', '\#', $this->sourceDir->getAbsolutePath());
        $source_dirpath = str_replace('[', '\[', $source_dirpath);
        $source_dirpath = str_replace(']', '\]', $source_dirpath);
        $source_dirpath = str_replace('(', '\(', $source_dirpath);
        $source_dirpath = str_replace(')', '\)', $source_dirpath);
        return preg_match('#^' . $source_dirpath . '/' . $this->getRegex() . '#', $file->getAbsolutePath()) ? TRUE : FALSE;
    }

    /**
     * Get the current road name
     * @return string
     */
    public final function getName() {
        return $this->name;
    }

    /**
     * Get the regex for file path under the sourceDir
     * @return string
     */
    public function getRegex() {
        return '.*$';
    }

    /**
     * Get the release directory path
     * @return File
     */
    public function getReleaseDir() {
        return $this->releaseDir;
    }

    /**
     * Get the resource object with specific internal resource class.<br>
     * If the factory returns null, then this file don't match this road and wouldn't 
     * be registered from this road.
     * @param File $file The resource as file object.
     * @return AbstractResourceFile The resource object from factory
     * @throws ResourceNotFoundException
     */
    protected function resourceFactory(File $file) {
        $matches = array();
        if (preg_match('#\.[a-zA-Z0-9]+$#', $file->getAbsolutePath(), $matches)) {
            switch (strtolower($matches[0])) {
                case '.css':
                    return new CssResourceFile($file);
                case '.less':
                    return new LessResourceFile($file);
                case '.js':
                    return new JsResourceFile($file);
                case '.png':
                    return new PngResourceFile($file);
                case '.jpg':
                case '.jpeg':
                    return new JpegResourceFile($file);
                case '.gif':
                    return new GifResourceFile($file);
            }
        }
        return new PlainResourceFile($file);
    }

    /**
     * Execute the release action for the resource
     * @param AbstractResourceFile $resource The source file to release.
     * @throws FileWriteErrorException
     */
    public function releaseResource(AbstractResourceFile $resource) {
        if (is_null($this->getReleaseDir())) {
            throw new Exception("ERROR: RELEASE DIR IS NULL: " . $resource->getRealPath());
        }
        if (!$this->getReleaseDir()->exists()) {
            $this->getReleaseDir()->mkdirs();
        }
        $release_file = $this->makeReleaseFile($resource);
        $release_dir = $release_file->getAbsoluteFile()->getParentFile();
        if (!$release_dir->exists()) {
            if (!$release_dir->mkdirs()) {
                throw new FileWriteErrorException("The directory '" . $release_dir->getAbsolutePath() . "' create fails.");
            }
        }
        \file_put_contents($release_file->getAbsolutePath(), $resource->getFileContents());
    }

    private $__release__file__map = array();

    /**
     * Generate the file object to release for the given resource.
     * 
     * @param AbstractResourceFile $resource
     * @return File
     */
    public final function makeReleaseFile(AbstractResourceFile $resource) {
        if (!isset($this->__release__file__map[$resource->getMemberId()])) {
            $this->__release__file__map[$resource->getMemberId()] = new File($this->makeReleaseRelativePath($resource), $this->getReleaseDir()->getAbsolutePath());
        }
        return $this->__release__file__map[$resource->getMemberId()];
    }

    /**
     * Generate the relative path to release for the given source.
     * @param AbstractResourceFile $resource The source.
     * @return string release path relative to the release dir in configure.
     * @throws FileWriteErrorException
     */
    protected function makeReleaseRelativePath(AbstractResourceFile $resource) {
        if (is_null($this->getReleaseDir())) {
            throw new FileWriteErrorException("The release dir configuration is missed for " . get_class($this));
        }
        return $resource->getRelativePath($this->getSourceDir());
    }

    private $__member__id = null;

    /**
     * Get this member object id.
     * @return string This member object identifier.
     */
    public final function getMemberId() {
        if (is_null($this->__member__id)) {
            $this->__member__id = uniqid();
        }
        return $this->__member__id;
    }

    private $__parent__project = null;

    /**
     * Gets the parent of this annotation.
     * 
     * @return Project The parent project of this annotation.
     */
    public final function getParentProject() {
        if (\is_null($this->__parent__project)) {
            $result = ProjectUtil::searchRelativeProject($this);
            if (\count($result) < 1) {
                throw new ProjectMemberNotFoundException("SOURCE ROAD NOT FOUND");
            }
            $this->__parent__project = $result[0];
        }
        return $this->__parent__project;
    }

    public function buildCache(AbstractResourceFile $resource) {
        
    }

}
