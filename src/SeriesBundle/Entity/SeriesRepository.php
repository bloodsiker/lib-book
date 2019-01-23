<?php

namespace SeriesBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class SeriesRepository
 */
class SeriesRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function baseSeriesQueryBuilder(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->where('s.isActive = 1')
            ->orderBy('s.id', 'DESC')
        ;

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $search
     *
     * @return QueryBuilder
     */
    public function searchBySeries(QueryBuilder $qb, string $search): QueryBuilder
    {
        return $qb->andWhere("s.title LIKE :search")->setParameter('search', '%'.$search.'%');
    }

    /**
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
//    public function getSeriesCount()
//    {
//        $qb = $this->baseSeriesQueryBuilder();
//
//        $qb->select('count(s.id)');
//
//        return $qb->getQuery()->getSingleScalarResult();
//    }
}
