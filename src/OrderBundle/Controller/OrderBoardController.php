<?php

namespace OrderBundle\Controller;

use OrderBundle\Entity\OrderBoard;
use OrderBundle\Entity\OrderBoardVotesResult;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Class OrderBoardController
 */
class OrderBoardController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function listAction(Request $request)
    {
        return $this->render('OrderBundle::orders_board_list.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function voteOrderAction(Request $request)
    {
        $orderId = $request->get('orderId');
        if ($orderId) {
            $em = $this->get('doctrine.orm.entity_manager');
            $repository = $em->getRepository(OrderBoard::class);
            $order = $repository->find((int) $orderId);
            if ($order) {
                if ($request->isXmlHttpRequest() && $request->getMethod() === 'POST') {
                    $ip = ip2long($request->server->get('REMOTE_ADDR'));
                    $resultVoted = $em->getRepository(OrderBoardVotesResult::class)
                        ->findOneBy(['ip' => $ip, 'orderBoard' => $order]);
                    if (!$resultVoted) {
                        $order->increaseVote();
                        $em->persist($order);

                        $resultVoted = new OrderBoardVotesResult();
                        $resultVoted->setOrderBoard($order);
                        $resultVoted->setIp($ip);
                        $em->persist($resultVoted);
                        $em->flush();

                        return new JsonResponse(['count' => $order->getVote()]);
                    } else {
                        // уже голосовали
                    }
                }
            }
        }

        return new JsonResponse();
    }
}