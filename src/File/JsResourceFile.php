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

use Chigi\Chiji\Collection\ResourcesCollection;

/**
 * Description of JsResourceFile
 *
 * @author 郷
 */
class JsResourceFile extends AbstractResourceFile implements RequiresMapInterface {

    private $requires;

    public function __construct($file_path) {
        parent::__construct($file_path);
        $this->requires = new ResourcesCollection();
    }

    /**
     * Get the 1-level requires resources map.
     * @return ResourcesCollection All the direct resources required by this file
     */
    public function getRequires() {
        return $this->requires;
    }

}
