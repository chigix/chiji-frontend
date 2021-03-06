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
 * Interface to be implemented by all web type resources.
 * @author 郷
 */
interface RequiresMapInterface {

    /**
     * Get the 1-level requires resources map.
     * @return ResourcesCollection All the direct resources required by this file
     */
    function getRequires();
}
