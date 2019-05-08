<?php

namespace QuizBundle\Block;

use AppBundle\Services\SaveStateValue;
use Doctrine\ORM\EntityManager;
use FOS\HttpCacheBundle\CacheManager;
use QuizBundle\Entity\Quiz;
use QuizBundle\Entity\QuizAnswer;
use QuizBundle\Entity\QuizResult;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

use Sonata\CoreBundle\Model\Metadata;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use NewMedia\VarnishCacheBundle\Annotation\VarnishTag;

/**
 * Class QuizBlockService
 * @VarnishTag(tags={
 *     {"name":"quiz.", "block_option": "quiz"},
 *     {"name":"quiz.list"}
 * })
 */
class QuizOriginBlockService extends AbstractAdminBlockService
{
    /**
     * Constants
     */
    const EXCLUDED_IDS = 'quiz_excluded_ids';
    const PREFIX_COOKIE = 'quiz_voted_';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var SaveStateValue
     */
    private $stateValue;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var string
     */
    private $domain;

    /**
     * QuizListBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param RequestStack    $request
     * @param EntityManager   $em
     * @param CacheManager    $cacheManager
     * @param string          $domain
     */
    public function __construct($name, EngineInterface $templating, RequestStack $request, EntityManager $em, CacheManager $cacheManager, $domain)
    {
        parent::__construct($name, $templating);

        $this->em       = $em;
        $this->request  = $request;
        $this->cacheManager  = $cacheManager;
        $this->domain  = $domain;
    }

    /**
     * @param SaveStateValue $stateValue
     */
    public function setStateValue(SaveStateValue $stateValue)
    {
        $this->stateValue = $stateValue;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => 'QuizBundle:Block:quiz_origin_item.html.twig',
            'quiz'     => null,
            'voted'    => true,
        ]);
    }

    /**
     * @param null $code
     *
     * @return Metadata
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata(
            $this->getName(),
            (!is_null($code) ? $code : $this->getName()),
            false,
            'QuizBundle',
            ['class' => 'fa fa-question-circle']
        );
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();
        $quiz = !is_null($block->getSetting('quiz')) ? $block->getSetting('quiz') : $blockContext->getSetting('quiz');
        if (!$quiz && !$request->isXmlHttpRequest()) {
            $quiz = $this->getQuiz();
        }
        if (!is_object($quiz)) {
            $quizId = $request->request->get('quiz') ?: $quiz;
            $quiz = $this->em
                ->getRepository(Quiz::class)
                ->find((int) $quizId)
            ;
        }

        if (!$quiz->getId()) {
            return new Response();
        }

        $ip = ip2long($request->server->get('REMOTE_ADDR'));
        $proxyIp = $request->server->get('HTTP_X_FORWARDED_FOR');
        $proxy = $proxyIp ? ip2long($proxyIp) : 0;

        $answers = array_map(
            'intval',
            $request->request->get('answers', [])
        );

        $voted = $blockContext->getSetting('voted');

        if ($request->isXmlHttpRequest() && $request->getMethod() === 'POST' && $answers) {
            $result = $this->em
                ->getRepository(QuizResult::class)
                ->findOneBy([
                    'ip'    => $ip,
                    'quiz'  => $quiz,
                    'proxy' => $proxy,
                ])
            ;

            if (!$result) {
                $quiz->setVotedCount($quiz->getVotedCount()+1);
                $this->em->persist($quiz);

                $quizAnswersCounterSum = 0;
                $quizAnswersCounter = [];
                foreach ($quiz->getQuizHasAnswer() as $item) {
                    /** @var QuizAnswer $answer */
                    $answer = $item->getAnswer();
                    $quizAnswersCounter[$answer->getId()] = $answer->getCounter();
                    $quizAnswersCounterSum += $answer->getCounter();

                    if (in_array($answer->getId(), $answers)) {
                        ++$quizAnswersCounter[$answer->getId()];
                        ++$quizAnswersCounterSum;
                    }
                }

                foreach ($quiz->getQuizHasAnswer() as $item) {
                    $answer = $item->getAnswer();

                    if (in_array($answer->getId(), $answers)) {
                        $answer->setCounter($quizAnswersCounter[$answer->getId()]);
                        $answer->setPercent($quizAnswersCounter[$answer->getId()]*100/$quizAnswersCounterSum);

                        $this->em->persist($answer);

                        $result = new QuizResult();
                        $result->setIp($ip);
                        $result->setQuiz($quiz);
                        $result->setProxy($proxy);
                        $result->setAnswer($answer);

                        $this->em->persist($result);
                    }
                }

                $this->em->flush();

                $this->cacheManager->invalidateTags(['quiz.'.$quiz->getId()]);
                $this->cacheManager->flush();
            }

            $voted = true;

            setcookie(self::PREFIX_COOKIE.$quiz->getId(), 'true', time()+3600*24*3650, '/', '.'.$this->domain);
        }

        $quizAnswers = $quiz->getQuizHasAnswer()->getValues();
        usort($quizAnswers, function ($a, $b) {
            return -1 * ($a->getAnswer()->getPercent() <=> $b->getAnswer()->getPercent());
        });

        return $this->renderResponse($blockContext->getTemplate(), [
            'quiz'       => $quiz,
            'block'      => $block,
            'voted'      => $voted,
            'quizAnswers' => $quizAnswers,
            'settings'   => array_merge($blockContext->getSettings(), $block->getSettings()),
            'blockID'    => 'quiz_'.$quiz->getId(),
        ], $response);
    }

    /**
     * @return Quiz $quiz|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function getQuiz()
    {
        $ids = $this->stateValue->getValue(self::EXCLUDED_IDS);
        $qb = $this->em->getRepository(Quiz::class)
            ->createBaseQuizList();

        if ($ids) {
            $qb->andWhere("q.id NOT IN (:ids)")->setParameter('ids', $ids);
        };

        $quiz = $qb
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();

        if ($quiz) {
            $this->stateValue->setExcludedIds(self::EXCLUDED_IDS, $quiz->getId());

            return $quiz;
        }

        return null;
    }
}
