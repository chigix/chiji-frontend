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

/**
 * Map for "SHORTCUT"=>"/path/to/target"
 *
 * @author 郷
 */
class PathMap extends \ArrayIterator {

    /**
     * Associates the specified path with the specified key in this map
     * @param string $key
     * @param string $path
     */
    public function put($key, $path) {
        $this->offsetSet($key, $path);
    }

    /**
     * Returns true if this map contains a mapping for the specified key.
     * @param string $key
     * @return boolean
     */
    public function containsKey($key) {
        return $this->offsetExists($key);
    }

    /**
     * Returns true if this map maps one or more keys to the specified path.
     * @param string $path
     * @return boolean
     */
    public function containsValue($path) {
        return in_array($path, array_values($this->getArrayCopy()));
    }

}
