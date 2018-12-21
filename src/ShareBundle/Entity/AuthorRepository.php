<?php

namespace ShareBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class AuthorRepository
 */
class AuthorRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function baseAuthorQueryBuilder(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->where('a.isActive = 1')
            ->orderBy('a.createdAt', 'DESC')
        ;

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $search
     *
     * @return QueryBuilder
     */
    public function searchByAuthor(QueryBuilder $qb, string $search): QueryBuilder
    {
        return $qb->andWhere("a.name LIKE :search")->setParameter('search', '%'.$search.'%');
    }

    /**
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAuthorsCount()
    {
        $qb = $this->baseAuthorQueryBuilder();

        $qb->select('count(a.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }
}
