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

/**
 * Description of AbstractAnnotation
 *
 * @author 郷
 */
abstract class AbstractAnnotation {

    /**
     *
     * @var Annotation
     */
    private $annotation;

    final public function __construct(Annotation $annotation) {
        $this->annotation = $annotation;
    }

    /**
     * Parse the param from the annotation, called from common annotation.
     * @param string $param_str The String as params following the command name
     */
    public abstract function parse($param_str);

    /**
     * Get the scope of the resource file object
     * @return AbstractResourceFile
     */
    protected function getScope() {
        if ($this->annotation->getScope() instanceof AbstractResourceFile) {
            return $this->annotation->getScope();
        } else {
            throw new ResourceNotFoundException("Invalid Annotation interface resource.");
        }
    }

    /**
     * Get the position of the occurrence in the current scope resource file.
     * @return int
     */
    protected function getOccursPos() {
        return $this->annotation->getOccursPos();
    }

}
