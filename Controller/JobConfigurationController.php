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
use Aureja\Bundle\JobQueueBundle\Form\Handler\JobPreConfigurationFormHandler;
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
     * @var JobConfigurationDeleteHandler
     */
    private $deleteHandler;

    /**
     * @var FormInterface
     */
    private $configurationForm;

    /**
     * @var JobConfigurationFormHandler
     */
    private $configurationFormHandler;

    /**
     * @var FormInterface
     */
    private $preConfigurationForm;

    /**
     * @var JobPreConfigurationFormHandler
     */
    private $preConfigurationFormHandler;

    /**
     * @var JobConfigurationManagerInterface
     */
    private $configurationManager;

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
     * @param JobConfigurationDeleteHandler $deleteHandler
     * @param FormInterface $configurationForm
     * @param JobConfigurationFormHandler $configurationFormHandler
     * @param FormInterface $preConfigurationForm
     * @param JobPreConfigurationFormHandler $preConfigurationFormHandler
     * @param JobConfigurationManagerInterface $configurationManager
     * @param RouterInterface $router
     * @param EngineInterface $templating
     */
    public function __construct(
        JobConfigurationDeleteHandler $deleteHandler,
        FormInterface $configurationForm,
        JobConfigurationFormHandler $configurationFormHandler,
        FormInterface $preConfigurationForm,
        JobPreConfigurationFormHandler $preConfigurationFormHandler,
        JobConfigurationManagerInterface $configurationManager,
        RouterInterface $router,
        EngineInterface $templating
    ) {
        $this->deleteHandler = $deleteHandler;
        $this->configurationForm = $configurationForm;
        $this->configurationFormHandler = $configurationFormHandler;
        $this->preConfigurationForm = $preConfigurationForm;
        $this->preConfigurationFormHandler = $preConfigurationFormHandler;
        $this->configurationManager = $configurationManager;
        $this->router = $router;
        $this->templating = $templating;
    }

    public function addAction(Request $request)
    {
        $configuration = $this->configurationManager->create();

        if ($this->preConfigurationFormHandler->process($configuration, $request)) {
            $this->preConfigurationFormHandler->onSuccess();

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
                'form' => $this->preConfigurationForm->createView(),
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

    public function editAction(Request $request, $configurationId)
    {
        $configuration = $this->getConfigurationOr404($configurationId);

        if ($this->configurationFormHandler->process($configuration, $request)) {
            $this->configurationFormHandler->onSuccess();

            return new RedirectResponse($this->router->generate('aureja_job_queue_configurations'));
        }

        return $this->templating->renderResponse(
            'AurejaJobQueueBundle:JobConfiguration:edit.html.twig',
            [
                'form' => $this->configurationForm->createView(),
            ]
        );
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
}
