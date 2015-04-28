<?php

/*
 * This file is part of the Aureja package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aureja\Bundle\JobQueueBundle\Form\Factory;

use Aureja\JobQueue\Model\JobConfigurationInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 4/28/15 8:56 PM
 */
class JobConfigurationFormFactory
{

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * Constructor.
     *
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * Create job configuration form.
     *
     * @param JobConfigurationInterface $configuration
     *
     * @return FormInterface
     */
    public function create(JobConfigurationInterface $configuration)
    {
        return $this->formFactory->create(
            'aureja_job_configuration',
            $configuration,
            [
                'job_factory' => $configuration->getFactory()
            ]
        );
    }
}
