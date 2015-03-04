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
