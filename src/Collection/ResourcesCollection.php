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

namespace Chigi\Chiji\Collection;

use ArrayIterator;
use Chigi\Chiji\File\AbstractResourceFile;

/**
 * Description of ResourcesCollection
 *
 * @author 郷
 */
class ResourcesCollection extends ArrayIterator {

    /**
     * Add the resource object to current collection<br/>
     * It will auto skip resources added, if you want to force add, please use append method.
     * @param AbstractResourceFile $resource
     */
    public function addResource(AbstractResourceFile $resource) {
        if (!parent::offsetExists($resource->getMemberId())) {
            parent::offsetSet($resource->getMemberId(), $resource);
        }
    }

    /**
     * Remove the target resource object from the current collection.
     * @param \Chigi\Chiji\File\AbstractResourceFile $resource
     */
    public function removeResource(AbstractResourceFile $resource) {
        if (parent::offsetExists($resource->getMemberId())) {
            parent::offsetUnset($resource->getMemberId());
        }
    }

}
