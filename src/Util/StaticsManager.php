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

namespace Chigi\Chiji\Util;

use Chigi\Chiji\Annotation\AbstractAnnotation;
use Chigi\Chiji\Annotation\FunctionAnnotation;

/**
 * Description of StaticsManager
 *
 * @author 郷
 */
class StaticsManager {

    private static $post_end_function_annotations = array();
    private static $customer_references = array();

    public static function registAnnotation(AbstractAnnotation $annotation) {
        if ($annotation instanceof FunctionAnnotation) {
            if (FunctionAnnotation::EXEC_IMMEDIATE === $annotation->getExecuteTime()) {
                $annotation->execute();
            } elseif (FunctionAnnotation::EXEC_POST_END === $annotation->getExecuteTime()) {
                array_push(self::$post_end_function_annotations, $annotation);
            }
        }
    }

    public static function getPostEndFunctionAnnotations() {
        return array_values(self::$post_end_function_annotations);
    }

    /**
     * Set a static to the manager
     * @param string $name
     * @param mixed $value
     */
    public static function setReferenece($name, $value) {
        self::$customer_references[$name] = $value;
    }

    /**
     * Get the target static from the manager
     * @param string $name
     * @return mixed
     */
    public static function getReference($name) {
        if (isset(self::$customer_references[$name])) {
            return self::$customer_references[$name];
        } else {
            return null;
        }
    }

}
