<?php

namespace AppBundle\Controller;

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
        return new Response();
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function contactsAction(Request $request)
    {
        return new Response("<h1>Contacts</h1>");
    }
}
