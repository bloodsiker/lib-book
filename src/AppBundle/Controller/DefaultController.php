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
            'title' => 'Topbook',
            'description' => 'Description',
            'keywords' => 'Keywords',
            'og' => [
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return new Response();
    }

    /**
     * @return Response
     */
    public function topBooksAction()
    {
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Топ-100 книг']);

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

        return $this->render('AppBundle:search:search.html.twig');
    }
}
