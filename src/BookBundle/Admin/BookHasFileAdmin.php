<?php

namespace BookBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class BookHasFileAdmin
 */
class BookHasFileAdmin extends Admin
{
    protected $parentAssociationMapping = 'book';

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('book', null, [
                'label' => 'article_has_authors.fields.article',
            ])
            ->add('bookFile', null, [
                'label' => 'article_has_authors.fields.author',
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
            ->add('bookFile', ModelListType::class, [
                'label' => 'article_has_authors.fields.author',
                'required' => true,
                'btn_edit' => true,
            ], ['link_parameters' => $linkParameters])
        ;
        if ($this->getSubject() && $this->getSubject()->getId()) {
            $formMapper
                ->add('bookFile.type', TextType::class, [
                    'label' => 'quiz_has_answer.fields.percent',
                    'required' => false,
                    'empty_data' => 0,
                    'attr' => [
                        'readonly' => true,
                        'disabled' => true,
                    ],
                ])
            ;
        }
    }
}
