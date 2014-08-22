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

use Chigi\Chiji\Collection\AnnotationCollection;

/**
 * Interface to be implemented by All web type resources with annotation enabled
 *
 * @author 郷
 */
interface Annotation {

    /**
     * Return All the annotation in the current resource file.
     * @return AnnotationCollection
     */
    public function getAnnotations();

    /**
     * Parse all the annotations in Resources Manager
     */
    public function analyzeAnnotations();
}
