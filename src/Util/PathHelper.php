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

use Chigi\Chiji\Exception\ResourceNotFoundException;

/**
 * HelperFunctions for path process
 *
 * @author 郷
 */
class PathHelper {

    /**
     * Get the realpath via searching from a filepath.
     * @param string $frmPath
     * @param string $targetPath
     * @return string
     */
    public static function searchRealPath($frmPath, $targetPath) {
        $real_path = null;
        if ('/' === substr($targetPath, 0, 1)) {
            $real_path = $targetPath;
        } elseif (preg_match('#^[a-zA-Z]:[\/\\\]#', $targetPath)) {
            $real_path = $targetPath;
        } else {
            // 均为相对路径
            if (is_dir($frmPath)) {
                $real_path = $frmPath . '/' . $targetPath;
            } else {
                // 均作为文件级处理
                $real_path = dirname($frmPath) . '/' . $targetPath;
            }
        }
        $real_path = str_replace('\\', '/', $real_path);
        while (strpos($real_path, '//') !== FALSE) {
            $real_path = str_replace('//', '/', $real_path);
        }
        $real_path_exploded = explode('..', $real_path);
        $real_path = $real_path_exploded[0];
        for ($i = 1; $i < count($real_path_exploded); $i++) {
            $real_path = dirname($real_path) . '/' . $real_path_exploded[$i];
        }
        $real_path = str_replace('//', '/', $real_path);
        return $real_path;
    }

}
