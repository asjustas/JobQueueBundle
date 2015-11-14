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

use Aureja\Bundle\JobQueueBundle\Form\Subscriber\AddJobParametersSubscriber;
use Aureja\Bundle\JobQueueBundle\Validator\Constraints\UniqueJobConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 4/28/15 10:54 PM
 */
class JobConfigurationType extends AbstractType
{

    /**
     * @var AddJobParametersSubscriber
     */
    private $subscriber;

    /**
     * @var array
     */
    private $queues;

    /**
     * @var string
     */
    private $configurationClass;

    /**
     * Constructor.
     *
     * @param AddJobParametersSubscriber $subscriber
     * @param array $queues
     * @param string $configurationClass
     */
    public function __construct(AddJobParametersSubscriber $subscriber, array $queues, $configurationClass)
    {
        $this->subscriber = $subscriber;
        $this->queues = $queues;
        $this->configurationClass = $configurationClass;
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
                    new Assert\Length(['min' => 3, 'max' => 255])
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
                'choices' => $this->getQueueChoices(),
                'empty_value' => 'select',
                'constraints' => new Assert\NotBlank(),
            ]
        );

        $builder->addEventSubscriber($this->subscriber);

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
        return 'aureja_job_configuration';
    }

    /**
     * @return array
     */
    private function getQueueChoices()
    {
        return array_combine($this->queues, $this->queues);
    }
}
