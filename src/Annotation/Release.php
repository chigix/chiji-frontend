<?php

/*
 * This file is part of the chiji-frontend package.
 * 
 * (c) Richard Lea <chigix@zoho.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chigi\Chiji\Annotation;

use Chigi\Chiji\Exception\ResourceNotFoundException;
use Chigi\Component\IO\File;

/**
 * Description of Release
 *
 * @author Richard Lea <chigix@zoho.com>
 */
class Release extends FunctionAnnotation {

    /**
     * The file path to release
     *
     * @var string
     */
    protected $path;

    public function execute() {
        if (is_null($resource = $this->getParentProject()->getResourceByFile($file_to_release = new File($this->path, $this->getScope()->getFile()->getAbsoluteFile()->getParent())))) {
            throw new ResourceNotFoundException($file_to_release->getAbsolutePath(), $this->getScope(), $this->getOccursPos(), "Releasing Resource NOT EXISTS");
        }
        $this->getParentProject()->getReleasesCollection()->addResource($resource);
    }

    public function getExecuteTime() {
        return parent::EXEC_IMMEDIATE;
    }

}
