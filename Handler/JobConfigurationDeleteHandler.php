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

use Aureja\JobQueue\JobState;
use Aureja\JobQueue\Model\JobConfigurationInterface;
use Aureja\JobQueue\Model\Manager\JobConfigurationManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 5/9/15 3:44 PM
 */
class JobConfigurationDeleteHandler
{

    /**
     * @var JobConfigurationManagerInterface
     */
    private $manager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Constructor.
     *
     * @param JobConfigurationManagerInterface $manager
     * @param TranslatorInterface $translator
     */
    public function __construct(JobConfigurationManagerInterface $manager, TranslatorInterface $translator)
    {
        $this->manager = $manager;
        $this->translator = $translator;
    }

    /**
     * Process job configuration delete.
     *
     * @param JobConfigurationInterface $configuration
     * @param Request $request
     *
     * @return bool
     */
    public function process(JobConfigurationInterface $configuration, Request $request)
    {
        if ($request->isMethod('DELETE') && (JobState::STATE_RUNNING !== $configuration->getState())) {
            $this->manager->remove($configuration);

            return true;
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
            'title' => $this->translator->trans('success.delete_configuration', [], 'AurejaJobQueue'),
            'type' => 'success'
        ];
    }
}
