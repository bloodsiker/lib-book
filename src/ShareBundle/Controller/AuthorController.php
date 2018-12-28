<?php

namespace ShareBundle\Controller;

use ShareBundle\Entity\Author;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Class AuthorController
 */
class AuthorController extends Controller
{
    const AUTHOR_404 = 'Author doesn\'t exist';

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
        $breadcrumb->addBreadcrumb(['title' => 'Авторы']);

        return $this->render('ShareBundle::author_list.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function authorBookAction(Request $request)
    {
        $slug = $request->get('slug');
        $repo = $this->getDoctrine()->getManager()->getRepository(Author::class);
        $author = $repo->findOneBy(['slug' => $slug, 'isActive' => true]);
        if (!$author) {
            throw $this->createNotFoundException(self::AUTHOR_404);
        }

        $router = $this->get('router');
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Авторы', 'href' => $router->generate('author_list')]);
        $breadcrumb->addBreadcrumb(['title' => $author->getName()]);

        return $this->render('ShareBundle::author_books.html.twig', ['author' => $author]);
    }
}