<?php

namespace BookBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use GenreBundle\Entity\Genre;

/**
 * Class BookCollectionRepository
 */
class BookCollectionRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function baseCollectionQueryBuilder(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('bc');
        $qb
            ->where('bc.isActive = 1')
            ->orderBy('bc.id', 'DESC')
        ;

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param Genre        $genre
     *
     * @return QueryBuilder
     */
    public function filterByGenre(QueryBuilder $qb, Genre $genre): QueryBuilder
    {
        return $qb->innerJoin('bc.genres', 'genre', 'WITH', 'genre.id = :genre')
            ->setParameter('genre', $genre);
    }

    /**
     * @return QueryBuilder
     */
    public function getGenresCollection()
    {
        $qb = $this->baseCollectionQueryBuilder();

        $qb
            ->addSelect('genre')
            ->innerJoin('bc.genres', 'genre');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $item
     */
    public function incViewCounter(int $item): void
    {
        $qb = $this->createQueryBuilder('bc');

        $qb
            ->update()
            ->set('bc.views', 'bc.views + 1')
            ->where('bc.id = :item')
            ->setParameter(':item', $item)
            ->getQuery()
            ->execute()
        ;
    }
}
