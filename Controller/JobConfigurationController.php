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

use Aureja\Bundle\JobQueueBundle\Form\Handler\JobConfigurationFormHandler;
use Aureja\Bundle\JobQueueBundle\Handler\JobConfigurationDeleteHandler;
use Aureja\Bundle\JobQueueBundle\Traits\ControllerTrait;
use Aureja\JobQueue\Model\Manager\JobConfigurationManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 4/24/15 9:41 PM
 */
class JobConfigurationController
{

    use ControllerTrait;

    /**
     * @var JobConfigurationManagerInterface
     */
    private $configurationManager;

    /**
     * @var JobConfigurationDeleteHandler
     */
    private $deleteHandler;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var JobConfigurationFormHandler
     */
    private $formHandler;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * Constructor.
     *
     * @param JobConfigurationManagerInterface $configurationManager
     * @param JobConfigurationDeleteHandler $deleteHandler
     * @param FormInterface $form
     * @param JobConfigurationFormHandler $formHandler
     * @param RouterInterface $router
     * @param EngineInterface $templating
     */
    public function __construct(
        JobConfigurationManagerInterface $configurationManager,
        JobConfigurationDeleteHandler $deleteHandler,
        FormInterface $form,
        JobConfigurationFormHandler $formHandler,
        RouterInterface $router,
        EngineInterface $templating
    ) {
        $this->configurationManager = $configurationManager;
        $this->deleteHandler = $deleteHandler;
        $this->form = $form;
        $this->formHandler = $formHandler;
        $this->router = $router;
        $this->templating = $templating;
    }

    public function listAction()
    {
        return $this->templating->renderResponse(
            'AurejaJobQueueBundle:JobConfiguration:list.html.twig',
            [
                'configurations' => $this->configurationManager->findAll(),
            ]
        );
    }

    public function addAction(Request $request)
    {
        $configuration = $this->configurationManager->create();

        if ($this->formHandler->process($configuration, $request)) {
            $this->formHandler->onSuccess();

            return new RedirectResponse(
                $this->router->generate(
                    'aureja_job_queue_configuration_edit',
                    ['configurationId' => $configuration->getId()]
                )
            );
        }

        return $this->templating->renderResponse(
            'AurejaJobQueueBundle:JobConfiguration:add.html.twig',
            [
                'form' => $this->form->createView(),
            ]
        );
    }

    public function editAction(Request $request, $configurationId)
    {
        $configuration = $this->getConfigurationOr404($configurationId);

        if ($this->formHandler->process($configuration, $request)) {
            $this->formHandler->onSuccess();

            return new RedirectResponse($this->router->generate('aureja_job_queue_configurations'));
        }

        return $this->templating->renderResponse(
            'AurejaJobQueueBundle:JobConfiguration:edit.html.twig',
            [
                'form' => $this->form->createView(),
            ]
        );
    }

    public function deleteAction(Request $request, $configurationId)
    {
        $configuration = $this->getConfigurationOr404($configurationId);

        if ($this->deleteHandler->process($configuration, $request)) {
            return new JsonResponse($this->deleteHandler->onSuccess());
        }

        return new JsonResponse($this->deleteHandler->onError());
    }
}
