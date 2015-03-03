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
use Chigi\Chiji\Exception\UndefinedReleaseUrlFormatException;
use Chigi\Chiji\File\AbstractResourceFile;
use Chigi\Chiji\File\CssResourceFile;
use Chigi\Chiji\File\GifResourceFile;
use Chigi\Chiji\File\JpegResourceFile;
use Chigi\Chiji\File\JsResourceFile;
use Chigi\Chiji\File\LessResourceFile;
use Chigi\Chiji\File\PlainResourceFile;
use Chigi\Chiji\File\PngResourceFile;
use Chigi\Chiji\Util\ResourcesManager;
use Chigi\Component\IO\File;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * Config for a release road for ONE RESOURCE. 
 * Generally only one type of resource road should be included in a Source Roadmap.
 *
 * @author 郷
 */
class SourceRoad {

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
            if (is_null(ResourcesManager::getResourceByFile($file))) {
                $resource = $this->resourceFactory($file);
                ResourcesManager::registerResource($resource);
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
        return preg_match('#^' . $source_dirpath . '/' . $this->getRegex() . '#', $file->getAbsolutePath()) ? TRUE : FALSE;
    }

    /**
     * Get the current road name
     * @return string
     */
    public function getName() {
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
     * Get the resource object with specific internal resource class.
     * @param string $resource_path Support absolute path ONLY.
     * @return AbstractResourceFile The resource object from factory
     * @throws ResourceNotFoundException
     */
    protected function resourceFactory($resource_path) {
        $tmp_resource = new PlainResourceFile($resource_path);
        $matches = array();
        if (preg_match('#\.[a-zA-Z0-9]+$#', $tmp_resource->getFile()->getAbsolutePath(), $matches)) {
            switch (strtolower($matches[0])) {
                case '.css':
                    return new CssResourceFile($tmp_resource->getFile());
                case '.less':
                    return new LessResourceFile($tmp_resource->getFile());
                case '.js':
                    return new JsResourceFile($tmp_resource->getFile());
                case '.png':
                    return new PngResourceFile($tmp_resource->getFile());
                case '.jpg':
                case '.jpeg':
                    return new JpegResourceFile($tmp_resource->getFile());
                case '.gif':
                    return new GifResourceFile($tmp_resource->getFile());
                default:
                    return $tmp_resource;
            }
        } else {
            return $tmp_resource;
        }
    }

    /**
     * Execute the release action for the resource
     * @param AbstractResourceFile $resource The source file to release.
     * @throws FileWriteErrorException
     */
    public function releaseResource(AbstractResourceFile $resource) {
        if (!$this->getReleaseDir()->exists()) {
            $this->getReleaseDir()->mkdirs();
        }
        $release_file = new File($this->makeReleaseRelativePath($resource), $this->getReleaseDir()->getAbsolutePath());
        $release_dir = $release_file->getParentFile();
        if (!$release_dir->exists()) {
            if (!$release_dir->mkdirs()) {
                throw new FileWriteErrorException("The directory '" . $release_dir->getAbsolutePath() . "' create fails.");
            }
        }
        file_put_contents($release_file->getAbsolutePath(), $resource->getFileContents());
    }

    /**
     * Returns the url formatted string accessed to the release result.
     * @param AbstractResourceFile $resource
     * @param string $format_name The name of the target format
     * @return string The result formatted string
     * @throws UndefinedReleaseUrlFormatException
     * @throws FileWriteErrorException
     */
    public function getReleaseFormatUrl(AbstractResourceFile $resource, $format_name) {
        $format_map = $this->getReleaseFormatMap();
        if (isset($format_map[$format_name])) {
            $url = str_replace('[FILE]', $this->makeReleaseRelativePath($resource), $format_map[$format_name]);
            switch ($this->getUrlStampType()) {
                case UrlStampEnum::NONE:
                    $url = str_replace('[STAMP]', '', $url);
                    break;
                case UrlStampEnum::TIME_STAMP:
                    $url = str_replace('[STAMP]', time(), $url);
                    break;
                case UrlStampEnum::TIME_HUMAN:
                    $url = str_replace('[STAMP]', date("YmdHis"), $url);
                    break;
                case UrlStampEnum::HASH:
                default:
                    $release_file = new File($this->makeReleaseRelativePath($resource), $this->getReleaseDir()->getAbsolutePath());
                    $url = str_replace('[STAMP]', substr(md5_file($release_file->getAbsolutePath()), 0, 8), $url);
                    break;
            }
            return $url;
        } else {
            throw new UndefinedReleaseUrlFormatException("The format_name '$format_name' not defined.");
        }
    }

    /**
     * Get the release format map.
     * @return array<typename==string>
     */
    protected function getReleaseFormatMap() {
        return array(
            "HTTP_URL" => "[FILE]?[STAMP]",
        );
    }

    /**
     * Returns the stamp type from the UrlStampEnum
     * @return int
     */
    protected function getUrlStampType() {
        return UrlStampEnum::NONE;
    }

    /**
     * Construct a file object to release for the source $resource.
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

}
