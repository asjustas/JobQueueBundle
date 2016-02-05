<?php

/*
 * This file is part of the Aureja package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aureja\Bundle\JobQueueBundle\Doctrine\EntityManager;

use Aureja\JobQueue\Model\JobConfigurationInterface;
use Aureja\JobQueue\Model\Manager\JobReportManager as BaseJobReportManager;
use Aureja\JobQueue\Model\JobReportInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 4/24/15 10:01 PM
 */
class JobReportManager extends BaseJobReportManager
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * Constructor.
     *
     * @param EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $this->class = $em->getClassMetadata($class)->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountByConfiguration(JobConfigurationInterface $configuration)
    {
        $qb = $this->repository->createQueryBuilder('jr');

        $qb
            ->select('COUNT(jr.id)')
            ->where($qb->expr()->eq('jr.configuration', ':configuration'))
            ->setParameter('configuration', $configuration);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getJobReportsByConfiguration(JobConfigurationInterface $configuration, $offset, $limit)
    {
        $qb = $this->repository->createQueryBuilder('jr');

        $qb
            ->select('jr')
            ->where($qb->expr()->eq('jr.configuration', ':configuration'))
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy($qb->expr()->desc('jr.startedAt'))
            ->setParameter('configuration', $configuration);

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getLastStartedByConfiguration(JobConfigurationInterface $configuration)
    {
        $qb = $this->repository->createQueryBuilder('jr');

        $qb
            ->select('jr')
            ->where($qb->expr()->eq('jr.configuration', ':configuration'))
            ->setMaxResults(1)
            ->orderBy($qb->expr()->desc('jr.startedAt'))
            ->setParameter('configuration', $configuration);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function add(JobReportInterface $report, $save = false)
    {
        $this->em->persist($report);
        if (true === $save) {
            $this->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(JobReportInterface $report, $save = false)
    {
        $this->em->remove($report);
        if (true === $save) {
            $this->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->em->clear($this->getClass());
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }
}
