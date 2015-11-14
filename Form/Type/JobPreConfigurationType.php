<?php

/*
 * This file is part of the Aureja package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aureja\Bundle\JobQueueBundle\Form\Type;

use Aureja\Bundle\JobQueueBundle\Validator\Constraints\UniqueJobConfiguration;
use Aureja\JobQueue\Provider\JobProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 4/22/15 10:38 PM
 */
class JobPreConfigurationType extends AbstractType
{

    /**
     * @var string
     */
    private $configurationClass;

    /**
     * @var JobProviderInterface
     */
    private $jobProvider;

    /**
     * @var array
     */
    private $queues;

    /**
     * Constructor.
     *
     * @param string $configurationClass
     * @param JobProviderInterface $jobProvider
     * @param array $queues
     */
    public function __construct($configurationClass, JobProviderInterface $jobProvider, array $queues)
    {
        $this->configurationClass = $configurationClass;
        $this->jobProvider = $jobProvider;
        $this->queues = $queues;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            'text',
            [
                'label' => 'name',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 3, 'max' => 255]),
                ],
            ]
        );

        $builder->add(
            'factory',
            'choice',
            [
                'label' => 'job_type',
                'choices' => $this->getFactoryNameChoices(),
                'empty_value' => 'select',
                'constraints' => new Assert\NotBlank(),
            ]
        );

        $builder->add(
            'queue',
            'choice',
            [
                'label' => 'queue',
                'choices' => $this->getQueueChoices(),
                'empty_value' => 'select',
                'constraints' => new Assert\NotBlank(),
            ]
        );

        $builder->add(
            'submit',
            'submit',
            [
                'label' => 'save'
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'constraints' => new UniqueJobConfiguration(),
                'data_class' => $this->configurationClass,
                'translation_domain' => 'AurejaJobQueue',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'aureja_job_pre_configuration';
    }

    /**
     * @return array
     */
    private function getFactoryNameChoices()
    {
        $names = $this->jobProvider->getFactoryNames();

        return array_combine($names, $names);
    }

    /**
     * @return array
     */
    private function getQueueChoices()
    {
        return array_combine($this->queues, $this->queues);
    }
}
