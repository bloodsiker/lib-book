<?php

namespace BookBundle\Admin;

use BookBundle\Entity\BookFile;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
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
                'label' => 'book_has_file.fields.book',
            ])
            ->add('bookFile', null, [
                'label' => 'book_has_file.fields.file',
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
                'label' => 'book_has_file.fields.file',
                'required' => true,
                'btn_edit' => true,
            ], ['link_parameters' => $linkParameters])
        ;
        if ($this->getSubject() && $this->getSubject()->getId()) {
            $formMapper
                ->add('bookFile.type', TextType::class, [
                    'label' => 'book_has_file.fields.type',
                    'required' => false,
                    'empty_data' => $this->getSubject()->getBookFile()->getType(),
                    'attr' => [
                        'readonly' => true,
                        'disabled' => true,
                        'value'    => $this->getType($this->getSubject()->getBookFile()->getType()),
                    ],
                ])
            ;
        }
    }

    /**
     * @param int $type
     *
     * @return mixed
     */
    protected function getType($type)
    {
        $types = BookFile::getTypeList();

        return $types[$type];
    }
}
