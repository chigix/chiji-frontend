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

namespace Chigi\Chiji\Util;

use Chigi\Chiji\Exception\ResourceNotFoundException;
use Chigi\Chiji\File\AbstractResourceFile;
use Chigi\Chiji\File\Annotation;
use Chigi\Chiji\File\PlainResourceFile;
use Chigi\Component\IO\File;

/**
 * Resource manager is a globally accessible stack for Registered ResourceFiles 
 * for project. This manager is a map implementation with a identification key 
 * generated from absolute filepath.
 *
 * @author 郷
 */
class ResourcesManager {

    private static $resources = array();

    /**
     * Return the resource file object if registered, <br/>
     * OR return null.
     * @param string $resource_id The resource id
     * @return AbstractResourceFile|NULL
     */
    public static function getResourceById($resource_id) {
        if (isset(self::$resources[$resource_id])) {
            return self::$resources[$resource_id];
        } else {
            return NULL;
        }
    }

    /**
     * Get the target Resource File Object Via given path<br/>
     * OR return null if not registered.
     * @param File $file
     * @return AbstractResourceFile|NULL
     * @throws ResourceNotFoundException
     */
    public static function getResourceByFile(File $file) {
        if ($file->isFile() && $file->exists()) {
            $tmp_resource = new PlainResourceFile($file);
            return self::getResourceById($tmp_resource->getId());
        } else {
            throw new ResourceNotFoundException("The " . $file->getAbsolutePath() . " NOT FOUND");
        }
    }

    /**
     * Regist a resource object to the manager
     * @param AbstractResourceFile $resource
     */
    public static function registerResource(AbstractResourceFile $resource) {
        if (!isset(self::$resources[$resource->getId()])) {
            self::$resources[$resource->getId()] = $resource;
            if ($resource instanceof Annotation) {
                $resource->analyzeAnnotations();
            }
        }
    }

    /**
     * List all the resources registered
     * @return array<AbstractResourceFile>
     */
    public static function getAll() {
        return array_values(self::$resources);
    }

}
