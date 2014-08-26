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

use Chigi\Chiji\Exception\FileWriteErrorException;
use Chigi\Chiji\File\AbstractResourceFile;
use Chigi\Chiji\Util\PathHelper;

/**
 * Description of LessRoad
 *
 * @author 郷
 */
class LessRoad extends SourceRoad {

    public function getRegex() {
        return '.+\.less$';
    }

    /**
     * Get the release format map.
     * @return array<typename==string>
     */
    protected function getReleaseFormatMap() {
        $map = parent::getReleaseFormatMap();
        $map['HTTP_URL'] = '[LESS_FILE]?[STAMP]';
        return $map;
    }

    public function getReleaseFormatUrl(AbstractResourceFile $resource, $format_name) {
        $url = parent::getReleaseFormatUrl($resource, $format_name);
        $relative_path = $resource->getRelativePath($this->getSourceDir());
        if ('.less' === substr($relative_path, -5)) {
            $relative_path = substr($relative_path, 0, -5) . '.css';
        } else {
            $relative_path .= '.css';
        }
        $url = str_replace('[LESS_FILE]', $relative_path, $url);
        return $url;
    }

    public function releaseResource(AbstractResourceFile $resource) {
        if (!is_null($this->getReleaseDir())) {
            $relative_path = $resource->getRelativePath($this->getSourceDir());
            if ('.less' === substr($relative_path, -5)) {
                $relative_path = substr($relative_path, 0, -5) . '.css';
            } else {
                $relative_path .= '.css';
            }
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
            $parser = new \Less_Parser();
            $parser->parseFile($resource->getRealPath());
            file_put_contents($release_path, $parser->getCss());
        }
    }

}
