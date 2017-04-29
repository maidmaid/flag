<?php

/*
 * This file is part of the maidmaid/flag package.
 *
 * (c) Dany Maillard <danymaillard93b@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maidmaid\Flag;

/**
 * Concrete Flag class that handles hierarchical bitfields.
 *
 * Some flags have hierarchical bitfields handle. For example, with the next flags:
 *
 * <code>
 * const VERBOSITY_VERBOSE = 64;
 * const VERBOSITY_VERY_VERBOSE = 128;
 * const VERBOSITY_DEBUG = 256;
 * </code>
 *
 * if <code>VERBOSITY_DEBUG</code> is flagged, <code>VERBOSITY_VERY_VERBOSE</code> and <code>VERBOSITY_VERBOSE</code>
 * will be implicitly flagged.
 *
 * @author Dany Maillard <danymaillard93b@gmail.com>
 */
class HierarchicalFlag extends Flag
{
    /**
     * {@inheritdoc}
     */
    public function has($flags)
    {
        return $flags <= $this->bitfield;
    }
}
