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

use Chigi\Chiji\Collection\ExtensionCollection;

/**
 *
 * @author Richard Lea <chigix@zoho.com>
 */
interface ProjectExtensionInterface {

    public function registerExtension(ExtensionCollection $extensions);
}
