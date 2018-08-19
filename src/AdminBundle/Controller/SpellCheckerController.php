<?php

namespace AdminBundle\Controller;

use AdminBundle\Helper\SpellCheckHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SpellCheckerController
 * @package AdminBundle\Controller
 */
class SpellCheckerController
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function indexAction(Request $request)
    {
        return new Response(
            json_encode(
                SpellCheckHelper::getInstance()->check($request->request)
            )
        );
    }

}
