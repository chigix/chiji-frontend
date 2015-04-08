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

use Chigi\Chiji\Exception\PathFixAnnotationException;
use Chigi\Chiji\Exception\ResourceNotFoundException;
use Chigi\Chiji\File\AbstractResourceFile;
use Chigi\Chiji\Util\PathFixManager;
use Chigi\Component\IO\File;
use Chigi\Component\IO\FileSystem;

/**
 * The `pathfix` expression:
 * `@pathfix a.js.twig with ../assets/a.js.twig`
 *
 * @author Richard Lea <chigix@zoho.com>
 */
class PathFixAnnotation extends AbstractAnnotation {

    /**
     *
     * @var string
     */
    protected $replaceString;

    /**
     *
     * @var AbstractResourceFile
     */
    protected $referenceResource;

    public function parse($param_str) {
        $matches = array();
        if (preg_match('/^([a-zA-Z\.\-_\/]+) with (.+)$/', trim($param_str), $matches)) {
            $this->replaceString = trim($matches[1]);
            $file = new File(trim($matches[2]), $this->getScope()->getFile()->getAbsoluteFile()->getParent());
            $this->referenceResource = $this->getParentProject()->getResourceByFile($file);
            if (\is_null($this->referenceResource)) {
                throw new ResourceNotFoundException($file->getAbsolutePath(), $this->getScope(), $this->getOccursPos(), "PathFix Reference Resource Not Found");
            }
        }  else {
            throw new PathFixAnnotationException("PathFix Syntax Error: " . $this->getRawContent());
        }
        PathFixManager::register($this, $this->getScope());
    }
    
    public function fix($releaseString) {
        $target_file = $this->getParentProject()->getMatchRoad($this->referenceResource->getFinalCache()->getFile())->makeReleaseFile($this->referenceResource->getFinalCache());
        $start_file = $this->getParentProject()->getMatchRoad($this->getScope()->getFinalCache()->getFile())->makeReleaseFile($this->getScope()->getFinalCache());
        $fixed_path = FileSystem::getFileSystem()->makePathRelative($target_file->getAbsoluteFile()->getParent(), $start_file->getAbsoluteFile()->getParent()) . $target_file->getName();
        $strings = \explode(\trim($this->getRawContent()), $releaseString);
        if (isset($strings[1])) {
            for($i = 1; $i < \count($strings); $i ++){
                if (($newline1 = \strpos($strings[$i], "\n")) === FALSE) {
                    continue;
                }
                if (($replace_str_pos = \strpos($strings[$i], $this->replaceString)) === FALSE) {
                    continue;
                }
                if (($newline2 = \strpos($strings[$i], "\n", $newline1)) === FALSE) {
                    $newline2 = \strlen($strings[$i]);
                }
                if ($replace_str_pos > $newline1 && $replace_str_pos < $newline2) {
                    $split_str1 = \substr($strings[$i], 0, $replace_str_pos);
                    $split_str2 = \substr($strings[$i], $replace_str_pos + \strlen($this->replaceString));
                    $strings[$i] = $split_str1 . $fixed_path . $split_str2;
                }
            }
            $releaseString = \implode($this->replaceString, $strings);
        }  else {
            if (($replace_str_pos = \strpos($releaseString, $this->replaceString)) === FALSE) {
                return $releaseString;
            }
            $split_str1 = \substr($releaseString, 0, $replace_str_pos);
            $split_str2 = \substr($releaseString, $replace_str_pos + \strlen($this->replaceString));
            $releaseString = $split_str1 . $fixed_path . $split_str2;
        }
        return $releaseString;
    }

}
