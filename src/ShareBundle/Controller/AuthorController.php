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
        $repo = $this->getDoctrine()->getManager()->getRepository(Author::class);
        $count = $repo->getAuthorsCount();

        return $this->render('ShareBundle::author_list.html.twig', ['countAuthors' => $count]);
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

        return $this->render('ShareBundle::author_books.html.twig', ['author' => $author]);
    }
}