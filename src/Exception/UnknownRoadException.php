<?php

/*
 * This file is part of the chiji-frontend package.
 * 
 * (c) Richard Lea <chigix@zoho.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chigi\Chiji\Exception;

use Chigi\Chiji\Collection\RoadMap;
use Chigi\Chiji\Project\SourceRoad;
use Exception;

/**
 * Thrown when trying to get non-exist SourceRoad name from a roadmap.
 *
 * @author Richard Lea <chigix@zoho.com>
 */
class UnknownRoadException extends Exception {

    /**
     * 
     * @param string $roadName
     * @param RoadMap $map
     */
    public function __construct($roadName, RoadMap $map) {
        $roads = array();
        foreach ($map as $road) {
            /* @var $road SourceRoad */
            \array_push($roads, $road->getName());
        }
        parent::__construct("Unknown Source Road Trying to get: [$roadName] FROM [" . \implode(",", $roads) . "]");
    }

}
