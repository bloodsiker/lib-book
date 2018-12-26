<?php

namespace AppBundle\Block;

use BookBundle\Entity\Book;
use Doctrine\ORM\EntityManager;
use Sonata\CoreBundle\Model\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Class SearchBlockService
 */
class SearchBlockService extends AbstractAdminBlockService
{
    const DEFAULT_TEMPLATE = 'AppBundle:Block:search.html.twig';
    const AJAX_TEMPLATE = 'AppBundle:Block:search_ajax_result.html.twig';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * HeaderBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param EntityManager   $em
     * @param RequestStack    $request
     */
    public function __construct($name, EngineInterface $templating, EntityManager $em, RequestStack $request)
    {
        parent::__construct($name, $templating);

        $this->em = $em;
        $this->request = $request;
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
            'AppBundle',
            ['class' => 'fa fa-th-large']
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'items_count' => 5,
            'template'    => self::DEFAULT_TEMPLATE,
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }
        $limit = (int) $blockContext->getSetting('items_count');
        $request = $this->request->getCurrentRequest();
        $search = $request->get('search');

        if ($search) {
            $repository = $this->em->getRepository(Book::class);

            if ($request->isXmlHttpRequest() && $request->getMethod() === 'POST') {
                $qb = $repository->createQueryBuilder('b');
                $qb
                    ->where('b.isActive = 1')
                    ->innerJoin('b.authors', 'author')
                    ->andWhere('b.name LIKE :search')
                    ->orWhere('author.name LIKE :search')
                    ->setParameter('search', '%'.$search.'%')
                    ->orderBy('b.views', 'DESC')
                    ->setFirstResult(0)
                    ->setMaxResults($limit)
                ;

                $results = $qb->getQuery()->getResult();
            }
        }

        return $this->renderResponse($request->isXmlHttpRequest() ? self::AJAX_TEMPLATE : $blockContext->getTemplate(), [
            'results'     => $results ?? [],
            'search'      => $search,
            'block'       => $block,
            'settings'    => array_merge($blockContext->getSettings(), $block->getSettings()),
        ]);
    }
}
