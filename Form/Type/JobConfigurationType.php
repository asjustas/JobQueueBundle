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

use Aureja\Bundle\JobQueueBundle\Validator\Constraints\UniqueJobConfigurationName;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 4/28/15 10:54 PM
 */
class JobConfigurationType extends AbstractType
{

    /**
     * @var string
     */
    private $configurationClass;


    /**
     * @var array
     */
    private $queues;

    /**
     * Constructor.
     *
     * @param string $configurationClass
     * @param array $queues
     */
    public function __construct($configurationClass, array $queues)
    {
        $this->configurationClass = $configurationClass;
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
                    new UniqueJobConfigurationName()
                ],
            ]
        );

        $builder->add(
            'enabled',
            'checkbox',
            [
                'label' => 'enabled',
                'required' => false,
            ]
        );

        $builder->add(
            'period',
            'integer',
            [
                'label' => 'period',
                'constraints' => new Assert\NotBlank(),
            ]
        );

        $builder->add(
            'queue',
            'choice',
            [
                'label' => 'queue',
                'choices' => array_combine($this->queues, $this->queues),
                'empty_value' => 'select',
                'constraints' => new Assert\NotBlank(),
            ]
        );

        $builder->add('parameters', $options['job_factory']);

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
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(['job_factory']);

        $resolver->setDefaults(
            [
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
        return 'aureja_job_configuration';
    }
}
