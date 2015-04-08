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

use Chigi\Component\IO\FileSystem;

/**
 * The `pathfixall` expression:
 * `@pathfixall a.js.twig with ../assets/a.js.twig`
 *
 * @author Richard Lea <chigix@zoho.com>
 */
class PathFixAllAnnotation extends PathFixAnnotation {

    public function fix($releaseString) {
        $target_file = $this->getParentProject()->getMatchRoad($this->referenceResource->getFinalCache()->getFile())->makeReleaseFile($this->referenceResource->getFinalCache());
        $start_file = $this->getParentProject()->getMatchRoad($this->getScope()->getFinalCache()->getFile())->makeReleaseFile($this->getScope()->getFinalCache());
        $fixed_path = FileSystem::getFileSystem()->makePathRelative($target_file->getAbsoluteFile()->getParent(), $start_file->getAbsoluteFile()->getParent()) . $target_file->getName();
        $strings = \explode($releaseString, $this->getRawContent());
        if (isset($strings[1])) {
            for ($i = 0; $i < count($strings); $i ++) {
                $strings[$i] = \str_replace($this->replaceString, $fixed_path, $strings[$i]);
            }
        }  else {
            return \str_replace($this->replaceString, $fixed_path, $releaseString);
        }
        return \implode($this->getRawContent(), $strings);
    }

}
