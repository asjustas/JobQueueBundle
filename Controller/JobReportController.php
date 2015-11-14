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

use Aureja\Bundle\JobQueueBundle\Traits\ControllerTrait;
use Aureja\JobQueue\Model\JobConfigurationInterface;
use Aureja\JobQueue\Model\Manager\JobConfigurationManagerInterface;
use Aureja\JobQueue\Model\Manager\JobReportManagerInterface;
use Silvestra\Component\Paginator\Pagination;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 11/14/15 7:00 PM
 */
class JobReportController
{

    use ControllerTrait;

    /**
     * @var JobConfigurationManagerInterface
     */
    private $configurationManager;

    /**
     * @var JobReportManagerInterface
     */
    private $reportManager;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * Constructor.
     *
     * @param JobConfigurationManagerInterface $configurationManager
     * @param JobReportManagerInterface $reportManager
     * @param EngineInterface $templating
     */
    public function __construct(
        JobConfigurationManagerInterface $configurationManager,
        JobReportManagerInterface $reportManager,
        EngineInterface $templating
    ) {
        $this->configurationManager = $configurationManager;
        $this->reportManager = $reportManager;
        $this->templating = $templating;
    }

    public function indexAction(Request $request, $configurationId)
    {
        $configuration = $this->getConfigurationOr404($configurationId);
        $pagination = $this->createPagination($configuration, $request);
        $reports = $this
            ->reportManager
            ->getJobReportsByConfiguration($configuration, $pagination->getOffset(), $pagination->getPerPage());

        return $this->templating->renderResponse(
            'AurejaJobQueueBundle:JobReport:index.html.twig',
            [
                'configuration' => $this->getConfigurationOr404($configurationId),
                'pagination' => $pagination,
                'reports' => $reports,
            ]
        );
    }

    /**
     * @param JobConfigurationInterface $configuration
     * @param Request $request
     *
     * @return Pagination
     */
    private function createPagination(JobConfigurationInterface $configuration, Request $request)
    {
        $count = $this->reportManager->getCountByConfiguration($configuration);

        return new Pagination($request->get('page', 1), 50, $count);
    }
}
