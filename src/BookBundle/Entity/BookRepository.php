<?php

namespace BookBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class BookRepository
 */
class BookRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function baseBookQueryBuilder()
    {
        $qb = $this->createQueryBuilder('b');
        $qb
            ->where('b.isActive = 1')
            ->orderBy('b.createdAt', 'DESC')
        ;

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param int          $period
     *
     * @return QueryBuilder
     *
     * @throws \Exception
     */
    public function filterPopularByDaysAgo(QueryBuilder $qb, int $period)
    {
        $timeAgo = $this->getNow()->modify(" -{$period} day");

        $qb
            ->andWhere('b.createdAt > :timeAgo')
            ->setParameter('timeAgo', $timeAgo)
            ->resetDQLPart('orderBy')
            ->orderBy('b.views', 'DESC');

        return $qb;
    }

    /**
     * @param array $tags
     * @param array $excludeIds
     * @param int   $limit
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getRelatedByTagsBooks(array $tags = [], array $excludeIds = [], $limit = 50)
    {
        $qb = $this->baseBookQueryBuilder();
        $qb
            ->innerJoin('b.tags', 'tag', 'WITH', 'tag.id IN (:tags)')
            ->setParameter('tags', $tags)
            ->setFirstResult(0)
            ->setMaxResults($limit)
        ;

        if ($excludeIds) {
            $qb->andWhere('b.id not in (:exclude_ids)')->setParameter('exclude_ids', $excludeIds);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Returns current date and time, rounded to nearest minute
     *
     * @return \DateTime
     *
     * @throws \Exception
     *
     * @todo move to helper class
     */
    public function getNow()
    {
        $now = new \DateTime('now');

        $now = new \DateTime($now->format('d-m-Y H:i:00'));

        return $now;
    }
}
