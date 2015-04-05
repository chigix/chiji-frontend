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

namespace Chigi\Chiji\Exception;

use Chigi\Chiji\File\AbstractResourceFile;
use Chigi\Chiji\Project\SourceRoad;

/**
 * Thrown when target AbstractResourceFile not found or not registered 
 * from annother resource with annotation or SourceRoad registering resource.
 *
 * @author 郷
 */
class ResourceNotFoundException extends \Exception {

    /**
     * 
     * @param string $resource_name
     * @param AbstractResourceFile|SourceRoad $searcher
     * @param int $position
     * @param string $reason
     */
    public function __construct($resource_name, $searcher, $position = null, $reason = null) {
        if ($searcher instanceof AbstractResourceFile) {
            $searcher_info = $searcher->getRealPath();
            if (is_int($position)) {
                $explode = \preg_split('/$\R?^/m', \substr($searcher->getFileContents(), 0, $position));
                $position = count($explode);
            }
        }  elseif ($searcher instanceof SourceRoad) {
            $searcher_info = get_class($searcher);
        }  else {
            throw new \InvalidArgumentException("Invalid Type for searcher: " . get_class($searcher));
        }
        parent::__construct((is_string($reason) ? $reason : "Resource Not Found") . ": [$resource_name] from [$searcher_info"
                . (is_int($position) ? (":" . $position) : "") . "]");
    }

}
