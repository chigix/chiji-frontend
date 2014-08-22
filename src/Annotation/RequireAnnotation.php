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

namespace Chigi\Chiji\Annotation;

use Chigi\Chiji\Exception\ResourceNotFoundException;
use Chigi\Chiji\File\AbstractResourceFile;
use Chigi\Chiji\Util\ResourcesManager;

/**
 * Use for `require` expression:
 * `@require ../bleach/bankai.js`
 *
 * @author 郷
 */
class RequireAnnotation extends AbstractAnnotation {

    /**
     *
     * @var AbstractResourceFile
     */
    private $requireResource = null;

    /**
     * Parse the path param in the require annotation to an Resource File Object.
     * @param string $param_str The String as params following the command name
     * @throws ResourceNotFoundException
     */
    public function parse($param_str) {
        $param_str = trim($param_str);
        $resource = $this->getScope();
        $real_path = null;
        if ('/' === substr($param_str, 0, 1)) {
            $real_path = realpath($param_str);
        } elseif (preg_match('#^[a-zA-Z]:[\/\\]#', $param_str)) {
            $real_path = realpath($param_str);
        } else {
            // 均为相对路径
            $real_path = realpath(dirname($resource->getRealPath()) . '/' . $param_str);
        }
        if ($real_path === FALSE) {
            throw new ResourceNotFoundException("The file $param_str NOT FOUND FROM " . $real_path->getRealPath());
        } else {
            $this->requireResource = ResourcesManager::getResourceByPath($real_path);
        }
    }

    /**
     * 
     * @return AbstractResourceFile
     */
    public function getResource() {
        return $this->requireResource;
    }

}
