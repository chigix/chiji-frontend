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
     * Parse all the annotations in this resource<br/>
     * Will be called explicitly.
     */
    public function analyzeAnnotations();
}
