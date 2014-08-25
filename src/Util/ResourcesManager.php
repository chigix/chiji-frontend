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

/**
 * Description of ResourcesManager
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
     * @param string $path
     * @return AbstractResourceFile|NULL
     * @throws ResourceNotFoundException
     */
    public static function getResourceByPath($path) {
        $real_path = realpath($path);
        if ($real_path === FALSE) {
            throw new ResourceNotFoundException("The $path NOT FOUND");
        }
        $tmp_resource = new PlainResourceFile($path);
        return self::getResourceById($tmp_resource->getId());
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
