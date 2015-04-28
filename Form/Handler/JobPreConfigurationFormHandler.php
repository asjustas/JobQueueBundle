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
     * @var JobConfigurationManagerInterface
     */
    private $configurationManager;

    /**
     * Constructor.
     *
     * @param JobConfigurationManagerInterface $configurationManager
     */
    public function __construct(JobConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * Process job pre configuration form.
     *
     * @param FormInterface $form
     * @param Request $request
     *
     * @return bool
     */
    public function process(FormInterface $form, Request $request)
    {
        if ($request->isMethod('POST')) {
            $form->submit($request);
            if ($form->isValid()) {
                $configuration = $form->getData();

                $configuration->setDisabled(true);
                $this->configurationManager->add($configuration);

                return true;
            }
        }

        return false;
    }

    /**
     * On success.
     */
    public function onSuccess()
    {
        $this->configurationManager->save();
    }
}
