<?php

/*
 * This file is part of the chiji-frontend package.
 * 
 * (c) Richard Lea <chigix@zoho.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chigi\Chiji\Project;

use Chigi\Chiji\Exception\InvalidConfigException;

/**
 * Chiji Front-end Intergration Solution Abstract Extension.
 *
 * @author Richard Lea <chigix@zoho.com>
 */
abstract class AbstractExtension {

    /**
     * Called When registering Project object.
     * 
     * @param Project $project
     * @throws InvalidConfigException
     */
    public function onRegister(Project $project) {
        
    }

    /**
     * Called When configuring extension from config.
     * 
     * @param ProjectConfig $config
     */
    public function onConfigure(ProjectConfig $config) {
        
    }

}
