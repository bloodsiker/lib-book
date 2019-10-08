<?php

namespace ArticleBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class ArticleHasBookAdmin
 */
class ArticleHasBookAdmin extends Admin
{
    protected $parentAssociationMapping = 'article';

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('article', null, [
                'label' => 'article_has_book.fields.article',
            ])
            ->add('book', null, [
                'label' => 'article_has_book.fields.book',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'delete' => [],
                ],
            ])
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $linkParameters = [];

        if ($this->hasParentFieldDescription()) {
            $linkParameters = $this->getParentFieldDescription()->getOption('link_parameters', []);
        }

        if ($this->hasRequest()) {
            $context = $this->getRequest()->get('context', null);

            if (null !== $context) {
                $linkParameters['context'] = $context;
            }
        }

        $formMapper
            ->add('book', ModelListType::class, [
                'label' => 'article_has_book.fields.book',
                'required' => true,
            ], ['link_parameters' => $linkParameters])
            ->add('orderNum', HiddenType::class, [
                'label' => 'article_has_book.fields.order_num',
            ]);
    }
}
