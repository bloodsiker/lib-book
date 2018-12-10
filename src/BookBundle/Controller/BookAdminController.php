<?php

namespace BookBundle\Controller;

use AdminBundle\Controller\CRUDController as Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BookAdminController
 */
class BookAdminController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function relatedByTagsAction(Request $request)
    {
//        $excludeIds = array_map(
//            function($value) { return (int) $value; },
//            $request->request->get('exclude', [])
//        );
//        $tags = array_map(
//            function($value) { return (int) $value; },
//            $request->request->get('tags', [])
//        );
//
//        $em = $this->container->get('doctrine')->getManager();
//        $repository = $em->getRepository($this->admin->getClass());
//        $relatedNews = $repository->getRelatedByTagsBooks($tags, $excludeIds, 40);
//
//        $result = array_map(
//            function($item) {
//                return [
//                    'id'        => $item->getId(),
//                    'name'      => $item->getName(),
//                    'date'      => $item->getPublishAt()->format('d.m.Y H:i:s'),
//                ];
//            },
//            $relatedNews
//        );
//
//        return $this->renderJson($result);
    }
}
