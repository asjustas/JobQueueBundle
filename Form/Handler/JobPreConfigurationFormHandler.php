<?php

/*
 * This file is part of the Aureja package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aureja\Bundle\JobQueueBundle\Form\Handler;

use Aureja\JobQueue\Model\JobConfigurationInterface;
use Aureja\JobQueue\Model\Manager\JobConfigurationManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 4/28/15 10:34 PM
 */
class JobPreConfigurationFormHandler
{

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var JobConfigurationManagerInterface
     */
    private $manager;

    /**
     * Constructor.
     *
     * @param FormInterface $form
     * @param JobConfigurationManagerInterface $manager
     */
    public function __construct(FormInterface $form, JobConfigurationManagerInterface $manager)
    {
        $this->form = $form;
        $this->manager = $manager;
    }

    /**
     * Process job pre configuration form.
     *
     * @param JobConfigurationInterface $configuration
     * @param Request $request
     *
     * @return bool
     */
    public function process(JobConfigurationInterface $configuration, Request $request)
    {
        $this->form->setData($configuration);
        $this->form->handleRequest($request);

        if ($this->form->isValid()) {
            $configuration->setEnabled(false);
            $this->manager->add($configuration);

            return true;
        }

        return false;
    }

    /**
     * On success.
     */
    public function onSuccess()
    {
        $this->manager->save();
    }
}
