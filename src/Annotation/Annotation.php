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
use Chigi\Chiji\File\Annotation as AnnotationInterface;
use Chigi\Chiji\Util\StaticsManager;

/**
 * The common annotation support for all resource file.
 *
 * @author 郷
 */
final class Annotation {

    use \Robo\Output;

    private $contents;
    private $scope;
    private $occursPos;

    /**
     * 
     * @param string $contents
     * @param AnnotationInterface $scope
     * @param int $occurs_pos
     */
    public function __construct($contents, AnnotationInterface $scope, $occurs_pos) {
        $this->contents = $contents;
        $this->scope = $scope;
        $this->occursPos = $occurs_pos;
    }

    /**
     * The main parse processor for annotaion text
     */
    public function parse() {
        /* @var $scope_resource AbstractResourceFile */
        $scope_resource = $this->getScope();
        if (!$scope_resource instanceof AbstractResourceFile) {
            throw new ResourceNotFoundException("INVALID scope resource file to parse.");
        }
        $contents = str_replace("\r", "\n", $this->contents);
        $annotation_lines = explode("\n", $contents);
        foreach ($annotation_lines as $annotation_line) {
            $annotation_line = trim($annotation_line);
            $matches = array();
            if ('@' !== substr($annotation_line, 0, 1)) {
                continue;
            }
            $this->say($annotation_line . '[' . $this->scope->getRealPath() . ']');
            if (preg_match('#^@([a-zA-Z]+)\s(.+)$#', $annotation_line, $matches)) {
                $command_name = $matches[1];
                $params = $matches[2];
            } elseif (preg_match('#^@([0-9a-zA-Z\/\\\]+)\((.*)\)$#', $annotation_line, $matches)) {
                $command_name = $matches[1];
                $params = $matches[2];
            } else {
                continue;
            }
            // Get the instance
            if ('require' === strtolower($command_name)) {
                $command_annotation = new RequireAnnotation($this);
            } elseif ('use' === strtolower($command_name)) {
                $command_annotation = new UseAnnotation($this);
            } elseif (class_exists($command_name)) {
                $command_annotation = new $command_name($this);
            } else {
                continue;
            }
            if ($command_annotation instanceof AbstractAnnotation) {
                $command_annotation->parse($params);
                StaticsManager::registAnnotation($command_annotation);
            }
        }
    }

    /**
     * Get the Position of the occurrence in current scope resource file.
     * @return int
     */
    public function getOccursPos() {
        return $this->occursPos;
    }

    /**
     * Get the current annotation interface instance as scope
     * @return AnnotationInterface
     */
    public function getScope() {
        return $this->scope;
    }

}
