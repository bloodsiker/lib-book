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

        return $this->render('SeriesBundle::series_books.html.twig', ['series' => $series]);
    }
}