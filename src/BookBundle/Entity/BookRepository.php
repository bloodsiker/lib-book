<?php

namespace BookBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use GenreBundle\Entity\Genre;
use ShareBundle\Entity\Tag;

/**
 * Class BookRepository
 */
class BookRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function baseBookQueryBuilder(): QueryBuilder
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
     * @param Genre        $genre
     *
     * @return QueryBuilder
     */
    public function filterByGenre(QueryBuilder $qb, Genre $genre): QueryBuilder
    {
        $genreIds = [$genre->getId()];
        if ($genre->getChildren()->count()) {
            foreach ($genre->getChildren()->getValues() as $child) {
                if ($child->getIsActive()) {
                    $genreIds[] = $child->getId();
                }
            }
        }

        return $qb->innerJoin('b.genres', 'genre', 'WITH', 'genre.id IN (:genre)')
            ->setParameter('genre', $genreIds);
    }

    /**
     * @param QueryBuilder $qb
     * @param Tag          $tag
     *
     * @return QueryBuilder
     */
    public function filterByTag(QueryBuilder $qb, Tag $tag) : QueryBuilder
    {

        return $qb->innerJoin('b.tags', 'tag', 'WITH', 'tag.id = :tag')->setParameter(
            'tag',
            $tag
        );
    }

    /**
     * @param QueryBuilder $qb
     * @param int          $period
     *
     * @return QueryBuilder
     *
     * @throws \Exception
     */
    public function filterPopularByDaysAgo(QueryBuilder $qb, int $period): QueryBuilder
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
