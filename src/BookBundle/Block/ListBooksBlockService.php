<?php

namespace BookBundle\Block;

use BookBundle\Entity\Book;
use BookBundle\Entity\BookRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Sonata\CoreBundle\Model\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ListBooksBlockService
 */
class ListBooksBlockService extends AbstractAdminBlockService
{
    const POPULAR_LIST = 'BookBundle:Block:popular_list.html.twig';

    /**
     * @var Registry $doctrine
     */
    protected $doctrine;

    /**
     * @var BookRepository
     */
    private $bookRepository;

    /**
     * ListGenreBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param Registry        $doctrine
     */
    public function __construct($name, EngineInterface $templating, Registry $doctrine)
    {
        parent::__construct($name, $templating);

        $this->doctrine = $doctrine;
    }

    /**
     * @param BookRepository $bookRepository
     */
    public function setBookRepository(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
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
            'BookBundle',
            ['class' => 'fa fa-th-large']
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'list_type'        => null,
            'items_count'      => 20,
            'popular'          => false,
            'popular_days_ago' => 30,
            'template'         => 'BookBundle:Block:large_list.html.twig',
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $block = $blockContext->getBlock();

        if (!$block->getEnabled()) {
            return new Response();
        }

        $limit = (int) $blockContext->getSetting('items_count');

//        $repository = $this->bookRepository;
        $repository = $this->doctrine->getRepository(Book::class);

        $qb = $repository->baseBookQueryBuilder($limit);

        $popularDaysAgo = $blockContext->getSetting('popular_days_ago');
        if ($blockContext->getSetting('popular') && $popularDaysAgo) {
            $repository->filterPopularByDaysAgo($qb, (int) $popularDaysAgo);
        }

        $result = $qb->getQuery()->getResult();

        $template = !is_null($blockContext->getSetting('list_type'))
            ? $blockContext->getSetting('list_type') : $blockContext->getTemplate();

        return $this->renderResponse($template, [
            'books'     => $result,
            'block'     => $block,
            'settings'  => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
