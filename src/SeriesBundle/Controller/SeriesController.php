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
        $repo = $this->getDoctrine()->getManager()->getRepository(Series::class);
        $count = $repo->getSeriesCount();

        return $this->render('SeriesBundle::series_list.html.twig', ['countSeries' => $count]);
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

        return $this->render('SeriesBundle::series_books.html.twig', ['series' => $series]);
    }
}