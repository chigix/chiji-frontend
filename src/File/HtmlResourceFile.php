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
use Chigi\Chiji\Collection\ResourcesCollection;

/**
 * Description of HtmlResourceFile
 *
 * @author 郷
 */
class HtmlResourceFile extends AbstractResourceFile implements RequiresMapInterface, Annotation {

    private $requires;
    private $annotations;

    public function __construct($file_path) {
        parent::__construct($file_path);
        $this->requires = new ResourcesCollection();
        $this->annotations = new AnnotationCollection();
        $this->parseComments();
    }

    /**
     * Parse all the annotations in Resources Manager<br/>
     * Launched by ResourcesManager<br/>
     * Built-in with annotations sort upon occurs position.
     */
    public final function analyzeAnnotations() {
        $annotation_ordered = array();
        foreach ($this->getAnnotations() as $annotation) {
            /* @var $annotation \Chigi\Chiji\Annotation\Annotation */
            $annotation_ordered[$annotation->getOccursPos()] = $annotation;
        }
        ksort($annotation_ordered);
        foreach ($annotation_ordered as $annotation) {
            /* @var $annotation \Chigi\Chiji\Annotation\Annotation */
            $annotation->parse();
        }
    }

    /**
     * Get the annotations in the current scope resource file
     * @return AnnotationCollection
     */
    public final function getAnnotations() {
        return $this->annotations;
    }

    /**
     * Get the 1-level requires resources map.
     * @return ResourcesCollection All the direct resources required by this file
     */
    public function getRequires() {
        return $this->requires;
    }

    public function parseComments() {
        $matches = array();
        preg_match_all('#\<![ \r\n\t]*(--([^\-]|[\r\n]|-[^\-])*--[ \r\n\t]*)\>#', $this->getFileContents(), $matches);
        $file_occurs_offset = 0;
        foreach ($matches[0] as $comment_str) {
            $occurs_pos = strpos($this->getFileContents(), $comment_str, $file_occurs_offset);
            $file_occurs_offset = $occurs_pos + strlen($comment_str);
            $comment_str = trim($comment_str);
            $start_pos = strpos($comment_str, '<!--') + 4;
            $end_pos = strrpos($comment_str, '-->');
            $comment_str = substr($comment_str, $start_pos, $end_pos - $start_pos);
            $this->annotations->addAnnotation(new \Chigi\Chiji\Annotation\Annotation($comment_str, $this, $occurs_pos));
        }
    }

}
