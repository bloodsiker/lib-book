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
        $excludeIds = array_map(function ($value) {
            return (int) $value;
            }, $request->request->get('exclude', []));

        $tags = array_map(function ($value) {
            return (int) $value;
            }, $request->request->get('tags', []));

        $em = $this->container->get('doctrine')->getManager();
        $repository = $em->getRepository($this->admin->getClass());
        $relatedNews = $repository->getRelatedByTagsBooks($tags, $excludeIds, 40);

        $result = array_map(
            function ($item) {
                $authors = [];
                array_map(function ($value) use (&$authors) {
                    $authors[] = $value->getName();

                    return $value;
                }, $item->getAuthors()->getValues());

                return [
                    'id'        => $item->getId(),
                    'name'      => $item->getName(),
                    'author'    => implode(', ', $authors),
                    'views'     => $item->getViews(),
                    'download'  => $item->getDownload(),
                    'rate'      => $item->getRatePlus() - $item->getRateMinus(),
                    'date'      => $item->getCreatedAt()->format('d.m.Y H:i:s'),
                ];
            },
            $relatedNews
        );

        return $this->renderJson($result);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function findTagsInTextAction(Request $request)
    {
        $excludeTags = array_map(
            function ($value) { return (int) $value; },
            $request->request->get('tags', [])
        );

        $bookFile = array_map(function ($value) {
            return (int) $value;
        }, $request->request->get('bookFile', []));

        $text = $request->request->get('textContent', '');


        $tagFinder = $this->container->get('share.tag.finder');
        $result = $tagFinder->findTagsInText($text, $excludeTags, $bookFile);

        return $this->renderJson($result);
    }
}
