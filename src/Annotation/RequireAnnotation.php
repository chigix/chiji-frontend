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
use Chigi\Chiji\File\RequiresMapInterface;
use Chigi\Component\IO\File;

/**
 * Use for `require` expression:
 * `@require ../bleach/bankai.js`
 *
 * @author 郷
 */
class RequireAnnotation extends AbstractAnnotation {

    /**
     * Parse the path param in the require annotation to an Resource File Object.
     * @param string $param_str The String as params following the command name
     * @throws ResourceNotFoundException
     */
    public function parse($param_str) {
        $file = new File(trim($param_str), $this->getScope()->getFile()->getAbsoluteFile()->getParent());
        if (!$file->exists()) {
            throw new ResourceNotFoundException("The file $param_str NOT FOUND FROM " . $file->getAbsolutePath());
        }
        if (is_null($require_resource = $this->getParentProject()->getResourceByFile($file))) {
            throw new \Chigi\Chiji\Exception\ProjectMemberNotFoundException("ERROR: UNREGISTERED Resource File: " + $file->getAbsolutePath());
        }
        if ($this->getScope() instanceof RequiresMapInterface) {
            $this->getScope()->getRequires()->addResource($require_resource);
        }
    }

}
