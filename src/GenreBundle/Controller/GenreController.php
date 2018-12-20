<?php

namespace GenreBundle\Controller;

use GenreBundle\Entity\Genre;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Class GenreController
 */
class GenreController extends Controller
{
    const GENRE_404 = 'Genre doesn\'t exist';

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function listAction(Request $request)
    {
        return $this->render('GenreBundle::genre_list.html.twig');
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
        $slugGenre = $request->get('genre');
        $slugSubGenre = $request->get('sub_genre');
        $repo = $this->getDoctrine()->getManager()->getRepository(Genre::class);
        $genre = $repo->findOneBy(['slug' => $slugGenre, 'isActive' => true]);
        if (!$genre) {
            throw $this->createNotFoundException(self::GENRE_404);
        }

        if ($slugSubGenre) {
            $subGenre = $repo->findOneBy(['slug' => $slugSubGenre, 'isActive' => true]);
            if (!$subGenre) {
                throw $this->createNotFoundException(self::GENRE_404);
            }
        }

        return $this->render('GenreBundle::book_list.html.twig', [
            'genre' => $genre,
            'subGenre' => $subGenre ?? null,
        ]);
    }
}