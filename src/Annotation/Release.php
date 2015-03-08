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

use Chigi\Chiji\Exception\InvalidConfigException;
use Chigi\Chiji\File\AbstractResourceFile;
use Chigi\Chiji\File\RequiresMapInterface;
use Chigi\Component\IO\File;

/**
 * Description of Release
 * `@Chigi\Chiji\Annotation\Release(type="css",path="./bankai.js",format="css")`
 *
 * @author 郷
 */
class Release extends FunctionAnnotation {

    use \Robo\Output;

    /**
     * The resources type to be released match
     * @var string 
     */
    protected $type;

    /**
     * The target release file path to write in.
     * @var string
     */
    protected $path;

    /**
     * The string format name for the URL accessed to write in.
     * @var string
     */
    protected $format;

    public function execute() {
        $resource = $this->getScope();
        $body_lines = array();
        // @TODO Get the resource type map from project.
        if ('css' === strtolower($this->type)) {
            $type = "\Chigi\Chiji\File\CssResourceFile";
        } elseif ('js' === strtolower($this->type)) {
            $type = '\Chigi\Chiji\File\JsResourceFile';
        }
        if ($resource instanceof RequiresMapInterface) {
            foreach ($resource->getRequires() as $resource_required) {
                /* @var $resource_required AbstractResourceFile */
                if ($resource_required instanceof $type) {
                    if (is_null($road = $this->getParentProject()->getMatchRoad($resource_required->getFinalCache()->getFile()))) {
                        throw new InvalidConfigException(sprintf("No roadmap for the resource cache '%s'.", $resource_required->getFile()));
                    } else {
                        $road->releaseResource($resource_required->getFinalCache());
                        $this->say('[RELEASED] ' . $resource_required->getFile()->getAbsolutePath());
                    }
                    array_push($body_lines, $road->getReleaseFormatUrl($resource_required->getFinalCache(), $this->format));
                    $this->say('[WRITEIN] ' . $this->format . ' ' . $resource_required->getFinalCache()->getRelativePath($road->getSourceDir()));
                }
            }
        }
        $release_file = new File($this->path, dirname($this->getScope()->getFile()->getAbsolutePath()));
        file_put_contents($release_file->getAbsolutePath(), implode("\n", $body_lines));
    }

    public function getExecuteTime() {
        return self::EXEC_POST_END;
    }

}
