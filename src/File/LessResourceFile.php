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

use Chigi\Component\IO\File;

/**
 * Description of LessResourceFile
 *
 * @author 郷
 */
class LessResourceFile extends CssResourceFile {

    public function __construct(File $file) {
        parent::__construct($file);
        $this->findComments();
    }

    private $stamp = null;

    public function getStamp() {
        if (is_null($this->stamp)) {
            $this->stamp = date("YmdHis");
        }
        return $this->stamp;
    }

    private function findComments() {
        $matches = array();
        if (!preg_match_all('#[ ]*[/]{2,}[[:blank:]]*(@.*)#', $this->getFileContents(), $matches) > 0) {
            return;
        }
        $file_occurs_offset = 0;
        foreach ($matches[1] as $comment_str) {
            $occurs_pos = strpos($this->getFileContents(), $comment_str, $file_occurs_offset);
            $file_occurs_offset = $occurs_pos + strlen($comment_str);
            $this->getAnnotations()->addAnnotation(new \Chigi\Chiji\Annotation\Annotation($comment_str, $this, $occurs_pos));
        }
    }

}
