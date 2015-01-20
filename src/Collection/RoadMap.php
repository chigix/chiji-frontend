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
use Chigi\Chiji\Project\SourceRoad;

/**
 * Description of RoadMap
 *
 * @author 郷
 */
class RoadMap extends ArrayIterator {

    private $position = 0;
    private $priorityQueue = array();

    /**
     * Associates the specified resource road with the road name in this map.
     * @param \Chigi\Chiji\Project\SourceRoad $road
     */
    private function put(SourceRoad $road) {
        $this->offsetSet($road->getName(), $road);
    }

    /**
     * Append a given resource road into this map.
     * 
     * @param SourceRoad $road
     * @return \Chigi\Chiji\Collection\RoadMap
     */
    public function append($road) {
        if (!in_array($road->getName(), $this->priorityQueue)) {
            $this->put($road);
            array_push($this->priorityQueue, $road->getName());
        }
        return $this;
    }

    public function prepend(SourceRoad $road) {
        if (!in_array($road->getName(), $this->priorityQueue)) {
            $this->put($road);
            array_unshift($this->priorityQueue, $road->getName());
        }
    }

    /**
     * Returns true if this map contains a mapping for the specified roadname.
     * @param string $road_name
     * @return boolean 
     */
    public function containsName($road_name) {
        return $this->offsetExists($road_name);
    }

    public function __construct($array = array(), $flags = 0) {
        parent::__construct($array, 0);
        $this->position = 0;
    }

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return parent::offsetGet($this->priorityQueue[$this->position]);
    }

    public function key() {
        return $this->priorityQueue[$this->position];
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset($this->priorityQueue[$this->position]);
    }

}
