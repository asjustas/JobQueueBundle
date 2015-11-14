<?php

/*
 * This file is part of the Aureja package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aureja\Bundle\JobQueueBundle\Handler;

use Aureja\JobQueue\Exception\JobConfigurationException;
use Aureja\JobQueue\JobRestoreManager;
use Aureja\JobQueue\Model\JobConfigurationInterface;
use Aureja\JobQueue\Model\Manager\JobConfigurationManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 5/9/15 4:06 PM
 */
class JobRestoreHandler
{

    /**
     * @var JobConfigurationManagerInterface
     */
    private $manager;

    /**
     * @var JobRestoreManager
     */
    private $restoreManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Constructor.
     *
     * @param JobConfigurationManagerInterface $manager
     * @param JobRestoreManager $restoreManager
     * @param TranslatorInterface $translator
     */
    public function __construct(
        JobConfigurationManagerInterface $manager,
        JobRestoreManager $restoreManager,
        TranslatorInterface $translator
    ) {
        $this->manager = $manager;
        $this->restoreManager = $restoreManager;
        $this->translator = $translator;
    }

    /**
     * Process job configuration delete.
     *
     * @param JobConfigurationInterface $configuration
     * @param Request $request
     *
     * @return bool
     *
     * @throws JobConfigurationException
     */
    public function process(JobConfigurationInterface $configuration, Request $request)
    {
        if (!function_exists('posix_getsid')) {
            throw new JobConfigurationException('Function posix_getsid don\'t exists');
        }

        if ($request->isMethod('POST')) {
            return $this->restoreManager->reset($configuration);
        }

        return false;
    }

    /**
     * On error.
     *
     * @return array
     */
    public function onError()
    {
        return [
            'title' => $this->translator->trans('an_error_occurred', [], 'AurejaJobQueue'),
            'type' => 'error'
        ];
    }

    /**
     * On success.
     *
     * @return array
     */
    public function onSuccess()
    {
        $this->manager->save();

        return [
            'title' => $this->translator->trans('success.restore_configuration', [], 'AurejaJobQueue'),
            'type' => 'success'
        ];
    }
}
