<?php

namespace BookBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class BookRepository
 */
class BookRepository extends EntityRepository
{
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
        $qb = $this->createQueryBuilder('b');
        $qb
            ->where('b.isActive = 1')
            ->innerJoin('b.tags', 'tag', 'WITH', 'tag.id IN (:tags)')
            ->orderBy('b.createdAt', 'DESC')
            ->setParameter('tags', $tags)
            ->setFirstResult(0)
            ->setMaxResults($limit)
        ;

        if ($excludeIds) {
            $qb->andWhere('b.id not in (:exclude_ids)')->setParameter('exclude_ids', $excludeIds);
        }

        return $qb->getQuery()->getResult();
    }
}
