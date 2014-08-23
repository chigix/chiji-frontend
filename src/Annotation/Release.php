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

use Chigi\Chiji\File\AbstractResourceFile;
use Chigi\Chiji\File\RequiresMapInterface;
use Chigi\Chiji\Util\PathHelper;

/**
 * Description of Release
 * `@Chigi\Chiji\Annotation\Release(type="css",path="./bankai.js")`
 *
 * @author 郷
 */
class Release extends FunctionAnnotation {

    /**
     * The resources type to be released match
     * @var string 
     */
    protected $type;

    /**
     * The target release path
     * @var string
     */
    protected $path;

    public function execute() {
        $resource = $this->getScope();
        $body_lines = array();
        if ('css' === strtolower($this->type)) {
            $type = "\Chigi\Chiji\File\CssResourceFile";
        }
        if ($resource instanceof RequiresMapInterface) {
            foreach ($resource->getRequires() as $resource_required) {
                /* @var $resource_required AbstractResourceFile */
                $resource_deploy_path = 'D:/Server/Hosts/F2/chigi_blog/web/chiji/' . $resource_required->getHash() . '.css';
                file_put_contents($resource_deploy_path, $resource_required->getFileContents());
                array_push($body_lines, '<link type="text/css" href="http://two/chigi_blog/web/chiji/' . $resource_required->getHash() . '.css" rel="stylesheet">');
            }
        }
        file_put_contents(PathHelper::searchRealPath($this->getScope()->getRealPath(), $this->path), implode("\n", $body_lines));
    }

    public function getExecuteTime() {
        return self::EXEC_POST_END;
    }

}
