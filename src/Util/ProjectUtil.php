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

use Chigi\Chiji\Exception\ConflictProjectNameException;
use Chigi\Chiji\Exception\ProjectNotFoundException;
use Chigi\Chiji\Project\MemberIdentifier;
use Chigi\Chiji\Project\Project;

/**
 * Description of ProjectUtil
 *
 * @author Richard Lea <chigix@zoho.com>
 */
class ProjectUtil {

    private static $REGISTERED_PRROJECT_INSTANCES;

    /**
     * Register a project object into this util manager.
     * @param Project $project
     * @param boolean $fresh_current Is to be set as current?
     * @throws ConflictProjectNameException
     */
    public static final function registerProject(Project $project) {
        if (isset(self::$REGISTERED_PRROJECT_INSTANCES[$project->getProjectName()])) {
            throw new ConflictProjectNameException(sprintf("The project \"%s\" EXISTS.", $project->getProjectName()));
        }
        self::$REGISTERED_PRROJECT_INSTANCES[$project->getProjectName()] = $project;
    }

    /**
     * Returns all the name-project map of the registered projects.
     * 
     * @return array{"project_name":Project}
     */
    public static final function getAllRegisteredProject() {
        return self::$REGISTERED_PRROJECT_INSTANCES;
    }

    /**
     * Searches the registered project for a given name and 
     * returns the corresponding object if successful.
     * @param string $project_name
     * @return Project
     * @throws ProjectNotFoundException
     */
    public static final function getRegisteredProject($project_name) {
        if (isset(self::$REGISTERED_PRROJECT_INSTANCES[$project_name])) {
            return self::$REGISTERED_PRROJECT_INSTANCES[$project_name];
        } else {
            throw new ProjectNotFoundException(sprintf("The project \"%s\" NOT registered.", $project_name));
        }
    }

    /**
     * Searches the Projects for a given member and returns the corresponding object array
     * if successful.
     * @param MemberIdentifier $member
     * @return array<Project> The relative project object to the given member.
     */
    public static final function searchRelativeProject(MemberIdentifier $member) {
        $result = array();
        foreach (self::$REGISTERED_PRROJECT_INSTANCES as $project) {
            /* @var $project Project */
            $members = $project->getAllMembers();
            if (isset($members[$member->getMemberId()])) {
                array_push($result, $project);
            }
        }
        return $result;
    }

}
