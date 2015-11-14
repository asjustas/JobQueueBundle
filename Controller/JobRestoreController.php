<?php

/*
 * This file is part of the Aureja package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aureja\Bundle\JobQueueBundle\Controller;

use Aureja\Bundle\JobQueueBundle\Handler\JobRestoreHandler;
use Aureja\Bundle\JobQueueBundle\Traits\ControllerTrait;
use Aureja\JobQueue\Model\Manager\JobConfigurationManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 5/9/15 4:32 PM
 */
class JobRestoreController
{

    use ControllerTrait;

    /**
     * @var JobConfigurationManagerInterface
     */
    private $configurationManager;

    /**
     * @var JobRestoreHandler
     */
    private $restoreHandler;

    /**
     * Constructor.
     *
     * @param JobConfigurationManagerInterface $configurationManager
     * @param JobRestoreHandler $restoreHandler
     */
    public function __construct(
        JobConfigurationManagerInterface $configurationManager,
        JobRestoreHandler $restoreHandler
    ) {
        $this->configurationManager = $configurationManager;
        $this->restoreHandler = $restoreHandler;
    }

    public function resetAction(Request $request, $configurationId)
    {
        $configuration = $this->getConfigurationOr404($configurationId);

        if ($this->restoreHandler->process($configuration, $request)) {
            return new JsonResponse($this->restoreHandler->onSuccess());
        }

        return new JsonResponse($this->restoreHandler->onError());
    }
}
