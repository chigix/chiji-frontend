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

/**
 * Description of FunctionAnnotation<br/>
 * `@Chigi\Chiji\Annotation\Release(type="css",path="./bankai.js")`
 *
 * @author 郷
 */
abstract class FunctionAnnotation extends AbstractAnnotation {

    const EXEC_IMMEDIATE = 0; // EXEC without wait
    const EXEC_POST_END = 1; // wait for the end of the compiling and then exec this function.

    public final function parse($param_str) {
        $params = explode(",", $param_str);
        foreach ($params as $param_item) {
            $param_kv = explode("=", $param_item);
            $param_key = trim($param_kv[0]);
            $param_value = trim($param_kv[1]);
            if ('"' === substr($param_key, 0, 1) && '"' === substr($param_key, -1, 1)) {
                $param_key = substr($param_key, 1, strlen($param_key) - 2);
            }
            if ('"' === substr($param_value, 0, 1) && '"' === substr($param_value, -1, 1)) {
                // This param_value is a string type, which could be set directly
                if (property_exists($this, $param_key)) {
                    $this->$param_key = substr($param_value, 1, strlen($param_value) - 2);
                } else {
                    // @TODO support special variables like from `@use`
                }
            }
        }
    }

    /**
     * @return int The Execute Time number
     */
    abstract public function getExecuteTime();

    abstract public function execute();
}
