<?php

/*
 * This file is part of the Aureja package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aureja\Bundle\JobQueueBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 4/30/15 10:26 PM
 */
class UniqueJobConfiguration extends Constraint
{
    public $message = 'aureja_job_queue.configurion_name_is_not_unique';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'aureja_job_queue.unique_job_configuration';
    }
}
