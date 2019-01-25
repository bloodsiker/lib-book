<?php

namespace SeriesBundle\Controller;

use SeriesBundle\Entity\Series;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Class SeriesController
 */
class SeriesController extends Controller
{
    const SERIES_404 = 'Series doesn\'t exist';

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function listAction(Request $request)
    {
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Серии']);
        $page = $request->get('page') ? " | Страница {$request->get('page', 1)}" : null;
        $pageDesc = $request->get('page') ? "Страница {$request->get('page', 1)} |" : null;

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'Серии'.$page.' | ТопБук - электронная библиотека',
            'description' => $pageDesc.'Серии | список книг по сериям | ТопБук - электронная библиотека. Тут Вы можете скачать бесплатно книги без регистрации',
            'keywords' => 'книги по сериям, скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, без регистрации, топбук',
            'og' => [
                'og:site_name' => 'TopBook.com.ua - электронная библиотека',
                'og:type' => 'website',
                'og:title' => 'Все серии | ТопБук',
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('SeriesBundle::series_list.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function listBookAction(Request $request)
    {
        $slug = $request->get('slug');
        $repo = $this->getDoctrine()->getManager()->getRepository(Series::class);
        $series = $repo->findOneBy(['slug' => $slug, 'isActive' => true]);
        if (!$series) {
            throw $this->createNotFoundException(self::SERIES_404);
        }

        $router = $this->get('router');
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Серии', 'href' => $router->generate('series_list')]);
        $breadcrumb->addBreadcrumb(['title' => 'Серия "'.$series->getTitle().'"']);

        $page = $request->get('page') ? " | Страница {$request->get('page', 1)}" : null;
        $pageDesc = $request->get('page') ? "Страница {$request->get('page', 1)} |" : null;

        $titleSeries = 'Серия '.$series->getTitle();
        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $titleSeries.' | Книги'.$page.' | ТопБук - электронная библиотека',
            'description' => "{$pageDesc} Скачать книги из серии {$series->getTitle()} бесплатно и без регистрации",
            'keywords' => $series->getTitle().', скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, без регистрации, топбук',
            'og' => [
                'og:site_name' => 'TopBook.com.ua - электронная библиотека',
                'og:type' => 'website',
                'og:title' => $titleSeries.' | Книги | Страница '.$request->get('page', 1).' | ТопБук',
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('SeriesBundle::series_books.html.twig', ['series' => $series]);
    }
}