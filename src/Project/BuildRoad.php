<?php

/*
 * Copyright 2015 Richard Lea <chigix@zoho.com>.
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

namespace Chigi\Chiji\Project;

use Chigi\Chiji\Exception\CacheBuildFileException;
use Chigi\Chiji\File\AbstractResourceFile;
use Chigi\Chiji\File\PlainResourceFile;
use Chigi\Component\IO\File;

/**
 * Description of BuildRoad
 *
 * @author Richard Lea <chigix@zoho.com>
 */
class BuildRoad extends SourceRoad {

    public function buildCache(AbstractResourceFile $resource) {
        $this->getParentProject()->getCacheManager()->registerDirectory($resource->getFile()->getAbsoluteFile()->getParentFile());
        $cache_dir = $this->getParentProject()->getCacheManager()->searchCacheDir($resource->getFile()->getAbsoluteFile()->getParentFile());
        if (is_null($cache_dir) || $cache_dir->isFile()) {
            throw new CacheBuildFileException("[" . $this->getParentProject()->getProjectName() . "]Cache Build Failed: " . $resource->getFile()->getAbsolutePath());
        }
        if (!$cache_dir->exists()) {
            $cache_dir->mkdirs();
        }
        $cache_file = new File($resource->getFile()->getName(), $cache_dir->getAbsolutePath());
        file_put_contents($cache_file->getAbsolutePath(), $resource->getFileContents());
        $this->getParentProject()->getCacheManager()->registerCache($resource, $cache_file);
    }
    
    protected function resourceFactory(File $file) {
        return new PlainResourceFile($file);
    }

}
