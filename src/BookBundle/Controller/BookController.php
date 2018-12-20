<?php

namespace BookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Class BookController
 */
class BookController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function viewAction(Request $request)
    {
        return $this->render('BookBundle::book_view.html.twig');
    }
}