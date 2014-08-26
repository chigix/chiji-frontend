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
use Chigi\Chiji\Util\PathHelper;
use Chigi\Chiji\Util\ResourcesManager;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * Config for a resource road
 *
 * @author 郷
 */
class SourceRoad {

    private $sourceDir;
    private $name;
    private $requires;
    private $useHash;
    private $charset;
    private $releaseDir;

    /**
     * 
     * @param string $name The roadname for the directory
     * @param string $source_dir The target directory path, support absolute Path ONLY.
     */
    function __construct($name, $source_dir, $release_dir = NULL) {
        $this->sourceDir = PathHelper::pathStandardize($source_dir);
        $this->releaseDir = PathHelper::pathStandardize($release_dir);
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
     * @return string
     */
    public final function getSourceDir() {
        return $this->sourceDir;
    }

    /**
     * Check if the resource match this road<br/>
     * if true then the resource could be get from manager.
     * @param string $file_path The resource file path to check, MUST BE ABSOLUTE PATH
     * @return boolean Returns true if the target path match the road
     */
    public final function resourceCheck($file_path) {
        if ($this->resourcePathMatch($file_path)) {
            if (is_null(ResourcesManager::getResourceByPath($file_path))) {
                $resource = $this->resourceFactory($file_path);
                ResourcesManager::registerResource($resource);
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Check if the resource match this road
     * @param string $file_path
     * @return boolean
     */
    protected function resourcePathMatch($file_path) {
        $source_dir = str_replace('#', '\#', $this->sourceDir);
        $file_path = PathHelper::pathStandardize($file_path);
        return preg_match('#^' . $source_dir . '/' . $this->getRegex() . '#', $file_path) ? TRUE : FALSE;
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
     * @return string|NULL
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
        if (preg_match('#\.[a-zA-Z0-9]+$#', $tmp_resource->getRealPath(), $matches)) {
            switch (strtolower($matches[0])) {
                case '.css':
                    return new CssResourceFile($tmp_resource->getRealPath());
                case '.less':
                    return new LessResourceFile($tmp_resource->getRealPath());
                case '.js':
                    return new JsResourceFile($tmp_resource->getRealPath());
                case '.png':
                    return new PngResourceFile($tmp_resource->getRealPath());
                case '.jpg':
                case '.jpeg':
                    return new JpegResourceFile($tmp_resource->getRealPath());
                case '.gif':
                    return new GifResourceFile($tmp_resource->getRealPath());
                default:
                    return $tmp_resource;
            }
        } else {
            return $tmp_resource;
        }
    }

    /**
     * Execute the release action for the resource
     * @param AbstractResourceFile $resource
     */
    public function releaseResource(AbstractResourceFile $resource) {
        if (!is_null($this->getReleaseDir())) {
            $relative_path = $resource->getRelativePath($this->getSourceDir());
            if (!is_dir($this->getReleaseDir())) {
                mkdir($this->getReleaseDir(), 0777, TRUE);
            }
            $release_path = PathHelper::searchRealPath($this->getReleaseDir(), $relative_path);
            $release_dir = dirname($release_path);
            if (!is_dir($release_dir)) {
                if (!mkdir($release_dir, 0777, TRUE)) {
                    throw new FileWriteErrorException("The directory '$release_dir' create fails.");
                }
            }
            file_put_contents($release_path, $resource->getFileContents());
        }
    }

    /**
     * Returns the url formatted string accessed to the release result.
     * @param AbstractResourceFile $resource
     * @param string $format_name The name of the target format
     * @return string The result formatted string
     * @throws UndefinedReleaseUrlFormatException
     */
    public function getReleaseFormatUrl(AbstractResourceFile $resource, $format_name) {
        $format_map = $this->getReleaseFormatMap();
        if (isset($format_map[$format_name])) {
            $url = str_replace('[FILE]', $resource->getRelativePath($this->getSourceDir()), $format_map[$format_name]);
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
                    $url = str_replace('[STAMP]', substr($resource->getHash(), 0, 8), $url);
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

}
