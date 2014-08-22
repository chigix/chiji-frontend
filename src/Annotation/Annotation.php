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
use Chigi\Chiji\File\RequiresMapInterface;

/**
 * The common annotation support for all resource file.
 *
 * @author 郷
 */
class Annotation {

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
            var_dump($annotation_line);
            preg_match('#^@(\S+)\s(.+)$#', $annotation_line, $matches);
            $command_name = strtolower($matches[1]);
            $params = $matches[2];
            switch ($command_name) {
                case 'require':
                    $command_annotation = new RequireAnnotation($this);
                    $command_annotation->parse($params);
                    if ($scope_resource instanceof RequiresMapInterface) {
                        $scope_resource->getRequires()->addResource($command_annotation->getResource());
                    }
                    break;
                case 'use':
                    var_dump("USE");
                    // new UseAnnotation($this);
                    break;
                default:
                    break;
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
