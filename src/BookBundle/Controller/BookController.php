<?php

namespace BookBundle\Controller;

use BookBundle\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Class BookController
 */
class BookController extends Controller
{
    const BOOK_404 = 'Book doesn\'t exist';

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function viewAction(Request $request)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(Book::class);
        $book = $repo->find($request->get('id'));
        if (!$book || !$book->getIsActive()) {
            throw $this->createNotFoundException(self::BOOK_404);
        }

        $router = $this->get('router');
        $breadcrumb = $this->get('app.breadcrumb');
        if ($book->getGenres()) {
            if ($book->getGenres()[0]->getParent()) {
                $genre = $book->getGenres()[0];
                $breadcrumb->addBreadcrumb([
                    'title' => $genre->getParent()->getName(),
                    'href' => $router->generate('sub_genre_books', ['genre' => $genre->getSlug(), 'sub_genre' => $genre->getParent()->getSlug()]),
                ]);
                $breadcrumb->addBreadcrumb([
                    'title' => $genre->getName(),
                    'href' => $router->generate('genre_books', ['genre' => $genre->getSlug()]),
                ]);
            } else {
                $breadcrumb->addBreadcrumb(['title' => $book->getGenres()[0]->getName(), 'href' => $router->generate('genre_books')]);
            }
        }
        $breadcrumb->addBreadcrumb(['title' => $book->getName()]);

        $repo->incViewCounter($book->getId());

        return $this->render('BookBundle::book_view.html.twig', ['book' => $book]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function incDownloadAction(Request $request)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(Book::class);
        $bookId = $request->get('bookId');
        $repo->incDownloadCounter($bookId);

        return new Response();
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function yearListAction(Request $request)
    {
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => $request->get('year').' год']);

        return $this->render('BookBundle::year_list.html.twig');
    }
}