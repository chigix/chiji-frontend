<?php

/*
 * This file is part of the chiji-frontend package.
 * 
 * (c) Richard Lea <chigix@zoho.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chigi\Chiji\Collection;

use Chigi\Chiji\Project\AbstractExtension;

/**
 * Description of ExtensionCollection
 *
 * @author Richard Lea <chigix@zoho.com>
 */
class ExtensionCollection extends \ArrayIterator {

    /**
     *
     * @var array<string> [md5(get_class(obj))]
     */
    private $hashStack;

    public function __construct($array = array(), $flags = 0) {
        parent::__construct($array, 0);
        $this->hashStack = array();
    }

    public function register(AbstractExtension $extension) {
        if (\in_array(\md5(\get_class($extension)), $this->hashStack)) {
            //
        } else {
            \array_push($this->hashStack, \md5(\get_class($extension)));
            $this->append($extension);
        }
    }

}
