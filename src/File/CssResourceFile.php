<?php

/*
 * This file is part of the chiji-frontend package.
 * 
 * (c) Richard Lea <chigix@zoho.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chigi\Chiji\File;

use Chigi\Chiji\Collection\AnnotationCollection;
use Chigi\Component\IO\File;

/**
 * Description of CssResourceFile
 *
 * @author 郷
 */
class CssResourceFile extends AbstractResourceFile implements Annotation {

    private $annotations;

    public function __construct(File $file) {
        parent::__construct($file);
        $this->annotations = new AnnotationCollection();
        $this->findComments();
    }

    public function analyzeAnnotations() {
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
     * Return All the annotation in the current resource file.
     * @return AnnotationCollection
     */
    public function getAnnotations() {
        return $this->annotations;
    }

    private function findComments() {
        $matches = array();
        if (!preg_match_all('#\/\*[^*]*\*+([^\/*][^*]*\*+)*\/#m', $this->getFileContents(), $matches) > 0) {
            return;
        }
        $file_occurs_offset = 0;
        foreach ($matches[0] as $comment_str) {
            $lines = explode("\n", $comment_str);
            foreach ($lines as $line_str) {
                $to_parse = array();
                if (preg_match('#^(\/\*|[\* -_])+(@.+)(\*\/|[\* -_])+$#', " " . trim($line_str) . " ", $to_parse)) {
                    $annotation_str = $to_parse[2];
                    $occurs_pos = strpos($this->getFileContents(), $annotation_str, $file_occurs_offset);
                    $file_occurs_offset = $occurs_pos + strlen($annotation_str);
                    $this->annotations->addAnnotation(new \Chigi\Chiji\Annotation\Annotation($annotation_str, $this, $occurs_pos));
                }
            }
        }
    }

}
