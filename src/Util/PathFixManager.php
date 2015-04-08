<?php

/*
 * This file is part of the chiji-frontend package.
 * 
 * (c) Richard Lea <chigix@zoho.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chigi\Chiji\Util;

use Chigi\Chiji\Annotation\PathFixAnnotation;
use Chigi\Chiji\File\AbstractResourceFile;
use Chigi\Chiji\File\RequiresMapInterface;

/**
 * Description of PathFixManager
 *
 * @author Richard Lea <chigix@zoho.com>
 */
class PathFixManager {

    /**
     *
     * @var array {AbstractResourceFile->memberId:PathFixAnnotation[]}
     */
    private static $resourceMapping = array();

    public static function register(PathFixAnnotation $fix, AbstractResourceFile $resource) {
        if (!array_key_exists($resource->getMemberId(), self::$resourceMapping)) {
            self::$resourceMapping[$resource->getMemberId()] = array();
        }
        array_push(self::$resourceMapping[$resource->getMemberId()], $fix);
    }

    /**
     * Fix Release Path during the resource's final release.
     * 
     * @param string $releaseString
     * @param AbstractResourceFile $resource
     * @return string
     */
    public static function fixReleasePath($releaseString, AbstractResourceFile $resource) {
        $fixes = self::getAllFixFromResource($resource);
        foreach ($fixes as $fix) {
            /* @var $fix PathFixAnnotation */
            $releaseString = $fix->fix($releaseString);
        }
        return $releaseString;
    }

    /**
     * Get All PathFix including requirements.
     * 
     * @param AbstractResourceFile $resource
     * @return PathFixAnnotation[]
     */
    public static function getAllFixFromResource(AbstractResourceFile $resource) {
        if (!array_key_exists($resource->getMemberId(), self::$resourceMapping)) {
            return array();
        }
        $fixes = self::$resourceMapping[$resource->getMemberId()];
        if ($resource instanceof RequiresMapInterface) {
            foreach ($resource->getRequires() as $requirement_resource) {
                /* @var $requirement_resource AbstractResourceFile */
                array_merge($fixes, self::getAllFixFromResource($requirement_resource));
            }
        }
        return $fixes;
    }

}
