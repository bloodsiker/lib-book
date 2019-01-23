<?php

namespace BookBundle\Controller;

use BookBundle\Entity\Book;
use MediaBundle\Entity\MediaFile;
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

        $page = $request->get('page') ? " | Страница {$request->get('page', 1)}" : null;

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'Последние новинки книг в библиотеке ТопБук'.$page,
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
        if ($book->getTags()->count()) {
            $tags = 'Теги: ';
            $book->getTags()->map(function ($value) use (&$tags) {
                $tags .= $value->getName().', ';

                return $value;
            });
        }

        $tags = $tags ?? null;
        $series = $book->getSeries() ? "Серия: {$book->getSeries()->getTitle()}, " : null;
        $title = $book->getName().' - '.$authors.' -  скачать книгу без регистрации в fb2, epub, pdf, txt | ТопБук - Электронная библиотека для любителей книг';

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $title,
            'description' => "Автор: {$authors}, ".$tags.$series.'Анотация: '.mb_substr($book->getDescription(), 0, 150),
            'keywords' => $book->getName().', '.$authors.', скачать книги, отзывы на книги, краткое содержание, без регистрации',
            'og' => [
                'og:site_name' => 'TopBook.com.ua - скачать книги без регистрации в fb2, epub, pdf, txt форматах',
                'og:type' => 'article',
                'og:title' => $title,
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
            'title' => 'Книги за '.$year.' год | ТопБук - Электронная библиотека для любителей книг',
            'description' => "Список книг за {$year} год | ТопБук - Электронная библиотека, скачать книги, читать рецензии, отзывы, книжные рейтинги.",
            'keywords' => 'скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, без регистрации, топбук',
            'og' => [
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('BookBundle::year_list.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function downloadAction(Request $request)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(MediaFile::class);
        $file = $repo->find((int) $request->get('file_id'));
        $baseWebDir = $this->getParameter('media_base_path');

        if (file_exists($baseWebDir.$file->getPath())) {
            $name = explode('/', $file->getPath());
            if (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename(end($name)));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: '.$file->getSize());

            readfile($baseWebDir.$file->getPath());
            die;
        }

        return $this->redirect($request->headers->get('referer'));
    }
}