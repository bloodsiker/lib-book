<?php

namespace SeriesBundle\Controller;

use AdminBundle\Controller\CRUDController as Controller;

use SeriesBundle\Entity\Series;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SeriesAdminController
 */
class SeriesAdminController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function searchSeriesAction(Request $request)
    {
        $name = trim($request->get('name'));
        $em = $this->container->get('doctrine')->getManager();
        $router = $this->container->get('router');
        $repository = $em->getRepository(Series::class);

        $qb = $repository->createQueryBuilder('s');
        $series = $qb
            ->where('s.isActive = 1')
            ->andWhere('s.title LIKE :search')
            ->setParameter('search', '%'.$name.'%')
            ->getQuery()->getResult();

        $result = array_map(
            function ($item) use ($router) {
                return [
                    'name' => $item->getTitle().' ['.Series::getNameType($item->getType())['class'].']',
                    'url'  => $router->generate('series_books', ['slug' => $item->getSlug()]),
                ];
            },
            $series
        );

        return $this->renderJson($result);
    }
}
