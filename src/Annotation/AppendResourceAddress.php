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
use Chigi\Chiji\Exception\ResourceNotFoundException;
use Chigi\Component\IO\File;
use Chigi\Component\IO\FileInputStream;
use Chigi\Component\IO\FileSystem;

/**
 * Description of Release
 * `@Chigi\Chiji\Annotation\Release(type="css",path="./bankai.js",format="css")`
 *
 * @author 郷
 */
class AppendResourceAddress extends FunctionAnnotation {

    use \Robo\Output;

    /**
     * The resources path
     * @var string 
     */
    protected $resource;

    /**
     * The target release file path to write in.
     * @var string
     */
    protected $output;

    /**
     * The string format name for the URL accessed to write in.
     * @var string
     */
    protected $template;

    public function execute() {
        if (is_null($this->resource)) {
            $exc_message = "Resource Path couldn't be detected.";
        } elseif (is_null($this->output)) {
            $exc_message = "Output Path occurs unhandled exception.";
        } elseif (is_null($this->template)) {
            $exc_message = "Template Path is incorrect.";
        }
        if (isset($exc_message) && is_string($exc_message)) {
            foreach ($this->getScope()->getAnnotations() as $annotation) {
                /* @var $annotation Annotation */
                if ($this->getOccursPos() === $annotation->getOccursPos()) {
                    throw new \Exception($exc_message . " : " . $annotation->getContents());
                }
            }
        }
        $file_required = new File($this->resource, $this->getScope()->getFile()->getAbsoluteFile()->getParent());
        $resource_required = $this->getParentProject()->getResourceByFile($file_required);
        if (is_null($resource_required)) {
            throw new ResourceNotFoundException($file_required->getAbsolutePath(), $this->getScope(), $this->getOccursPos(), "Appending Resource NOT REGISTERED");
        }
        $template = new FileInputStream(new File($this->template, $this->getScope()->getRealPath()));
        $append_file = new File($this->output, $this->getScope()->getRealPath());
        $format = trim($template->readAll());
        if (is_null($road = $this->getParentProject()->getMatchRoad($resource_required->getFinalCache()->getFile()))) {
            throw new InvalidConfigException(\sprintf("No roadmap for the resource cache '%s'.", $resource_required->getFile()->getAbsolutePath()));
        } else {
            $road->releaseResource($resource_required->getFinalCache());
            $this->say('[APPENDED] ' . $resource_required->getFile()->getAbsolutePath());
        }
        $address_required = $this->getParentProject()->getMatchRoad($resource_required->getFinalCache()->getFile())->makeReleaseFile($resource_required->getFinalCache())->getAbsoluteFile();
        $address_scope = $this->getParentProject()->getMatchRoad($this->getScope()->getFinalCache()->getFile())->makeReleaseFile($this->getScope()->getFinalCache())->getAbsoluteFile();
        $address_result = str_replace('[FILE]', FileSystem::getFileSystem()->makePathRelative($address_required->getParent(), $address_scope->getParent()) . $address_required->getName(), $format);
        $address_result = str_replace('[STAMP]', $resource_required->getFinalCache()->getStamp(), $address_result);
        \file_put_contents($append_file->getAbsolutePath(), $address_result . "\n", \FILE_APPEND);
        $this->say('[WRITEIN] ' . $template->getFile()->getName() . ' ' . $resource_required->getFinalCache()->getRelativePath($road->getSourceDir()));
    }

    public function getExecuteTime() {
        return parent::EXEC_POST_END;
    }

}
