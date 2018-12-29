<?php

namespace CommentBundle\Controller;

use CommentBundle\Entity\Comment;
use CommentBundle\Entity\CommentVotesResult;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Class CommentController
 */
class CommentController extends Controller
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
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Последние комментарии']);

        return $this->render('CommentBundle::last_comments_list.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function commentVoteAction(Request $request)
    {
        $commentId = $request->get('commentId');
        if ($commentId) {
            $em = $this->get('doctrine.orm.entity_manager');
            $repository = $em->getRepository(Comment::class);
            $comment = $repository->find((int) $commentId);
            if ($comment) {
                if ($request->isXmlHttpRequest() && $request->getMethod() === 'POST') {
                    $ip = ip2long($request->server->get('REMOTE_ADDR'));
                    $vote = (bool) $request->request->get('vote');
                    $resultVoted = $em->getRepository(CommentVotesResult::class)
                        ->findOneBy(['ip' => $ip, 'comment' => $comment]);
                    if (!$resultVoted) {
                        if (true === $vote) {
                            $comment->increaseVote();
                        } else {
                            $comment->decreaseVote();
                        }

                        $resultVoted = new CommentVotesResult();
                        $resultVoted->setComment($comment);
                        $resultVoted->setIp($ip);
                        $resultVoted->setResultVote($vote);

                        $em->persist($comment);
                        $em->persist($resultVoted);
                        $em->flush();

                        return new JsonResponse([
                            'count' => $comment->getRating(),
                            'message' => 'Вы проголосовали за комментарий',
                            'type' => 'success',
                        ]);
                    } else {
                        $message = 'Вы изменили свое решение по этому комментарию';
                        $type = 'success';
                        if (true === $vote && false === $resultVoted->getResultVote()) {
                            $resultVoted->setResultVote($vote);
                            $comment->increaseVote();
                            $comment->increaseVote();
                        } elseif (false === $vote && true === $resultVoted->getResultVote()) {
                            $resultVoted->setResultVote($vote);
                            $comment->decreaseVote();
                            $comment->decreaseVote();
                        } else {
                            $message = 'Вы уже голосовали за этот комментарий';
                            $type = 'error';
                        }

                        $em->persist($comment);
                        $em->persist($resultVoted);
                        $em->flush();

                        return new JsonResponse([
                            'count' => $comment->getRating(),
                            'message' => $message,
                            'type' => $type,
                        ]);
                    }
                }
            }
        }

        return new JsonResponse();
    }
}