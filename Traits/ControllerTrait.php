<?php

/*
 * This file is part of the Aureja package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aureja\Bundle\JobQueueBundle\Traits;

use Aureja\JobQueue\Model\JobConfigurationInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 11/14/15 5:12 PM
 */
trait ControllerTrait
{

    /**
     * Get job configuration or 404.
     *
     * @param int $configurationId

     * @return JobConfigurationInterface
     *
     * @throws NotFoundHttpException
     */
    private function getConfigurationOr404($configurationId)
    {
        $configuration = $this->configurationManager->find($configurationId);

        if (null === $configuration) {
            throw new NotFoundHttpException();
        }

        return $configuration;
    }
}
