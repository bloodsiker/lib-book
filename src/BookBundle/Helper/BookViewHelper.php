<?php

namespace BookBundle\Helper;

use BookBundle\Entity\Book;
use BookBundle\Entity\BookInfoView;
use Doctrine\ORM\EntityManager;

/**
 * Class BookViewHelper
 */
class BookViewHelper
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * ArticleExtension constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {

        $this->entityManager = $entityManager;
    }

    /**
     * @param Book $book
     *
     * @return bool
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function doView(Book $book)
    {
        $repository = $this->entityManager->getRepository(BookInfoView::class);

        $now = new \DateTime('now');
        $viewBook = $repository->findOneBy(['book' => $book, 'viewAt' => $now]);

        if ($viewBook) {
            $viewBook->doView();

            $this->entityManager->persist($viewBook);
            $this->entityManager->flush();
        } else {
            $viewBook = new BookInfoView();
            $viewBook->doView();
            $viewBook->setBook($book);
            $viewBook->setViewAt($now);

            $this->entityManager->persist($viewBook);
            $this->entityManager->flush();
        }

        return true;
    }
}
