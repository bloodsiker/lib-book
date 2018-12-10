<?php

namespace ShareBundle\Services;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SocialService
 */
class SocialService
{
    /**
     * @var EntityManagerInterface
     */
    protected $manager;

    /**
     * @var \ShareBundle\Entity\SocialRepository
     */
    protected $repository;

    /**
     * SocialService constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->repository = $this->manager->getRepository('ShareBundle:Social');
    }

    /**
     * @param boolean $onlyActive
     *
     * @return array|\ShareBundle\Entity\Social[]
     */
    public function getAll($onlyActive = true)
    {
        return $this->repository->findBy(array('isActive' => $onlyActive));
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository|\ShareBundle\Entity\SocialRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }
}
