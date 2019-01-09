<?php

namespace AppBundle\Controller;

use BookBundle\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 */
class DefaultController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
//        $metaTitle = $this->get('translator')->trans('app.frontend.meta.meta_title_index', ['%YEAR%' => date("Y")], 'AppBundle');
//        $metaDescription = $this->get('translator')->trans('app.frontend.meta.meta_description_index', ['%YEAR%' => date("Y")], 'AppBundle');
//        $metaKeywords = $this->get('translator')->trans('app.frontend.meta.meta_keywords_index', ['%YEAR%' => date("Y")], 'AppBundle');
        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'TopBook.com.ua - скачать книги в fb2, epub, pdf, txt форматах',
            'description' => 'Электронная библиотека, скачать книги, читать рецензии, отзывы, книжные рейтинги.',
            'keywords' => 'скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, топбук',
            'og' => [
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return new Response();
    }

    /**
     * @return Response
     */
    public function topBooksAction(Request $request)
    {
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Топ-100 книг']);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => '100 лучших книг на сайте | TopBook.com.ua - скачать книги в fb2, epub, pdf, txt форматах',
            'description' => 'Электронная библиотека, скачать книги, читать рецензии, отзывы, книжные рейтинги.',
            'keywords' => 'скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, топбук',
            'og' => [
                'og:site_name' => 'TopBook.com.ua - скачать книги в fb2, epub, pdf, txt форматах',
                'og:type' => 'article',
                'og:title' => '100 лучших книг на сайте',
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('BookBundle::top-100.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function searchAction(Request $request)
    {
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Поиск']);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => '100 лучших книг на сайте | TopBook.com.ua - скачать книги в fb2, epub, pdf, txt форматах',
            'description' => 'ТопБук - электронная библиотека. Тут Вы можете скачать бесплатно книги',
            'keywords' => 'скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, топбук',
        ]);

        return $this->render('AppBundle:search:search.html.twig');
    }
}
