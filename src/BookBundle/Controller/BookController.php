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
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @Cache(maxage=60, public=true)
     */
    public function listAction(Request $request)
    {
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Новинки книг']);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'Последние новинки книг в библиотеке "Топбук"',
            'description' => 'Электронная библиотека, скачать книги, читать рецензии, отзывы, книжные рейтинги.',
            'keywords' => 'скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, топбук',
            'og' => [
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('BookBundle::book_list.html.twig');
    }

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

        $authors = '';
        $book->getAuthors()->map(function ($value) use (&$authors) {
            $authors .= $value->getName().', ';

            return $value;
        });
        $authors = mb_substr($authors, 0, -2);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $book->getName().' - '.$authors.' -  скачать книгу в fb2, epub, pdf, txt | "Тоббук" - социальная сеть любителей книг',
            'description' => mb_substr($book->getDescription(), 0, 150),
            'keywords' => $book->getName().', '.$authors.', скачать книги, отзывы на книги, краткое содержание',
            'og' => [
                'og:site_name' => 'Topbook.com.ua - скачать книги в fb2, epub, pdf, txt форматах',
                'og:type' => 'article',
                'og:title' => $book->getName(),
                'og:url' => $request->getSchemeAndHttpHost(),
                'og:image' => $request->getSchemeAndHttpHost().$book->getPoster()->getPath(),
                'og:description' => mb_substr($book->getDescription(), 0, 150),
            ],
        ]);

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
        $year = $request->get('year');
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => $year.' год']);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'Книги за '.$year.' год | "Тоббук" - социальная сеть любителей книг',
            'description' => 'Электронная библиотека, скачать книги, читать рецензии, отзывы, книжные рейтинги.',
            'keywords' => 'скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, топбук',
            'og' => [
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('BookBundle::year_list.html.twig');
    }
}