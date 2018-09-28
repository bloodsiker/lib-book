<?php

namespace BookBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichFileType;

/**
 * Class BookFileAdmin
 */
class BookFileAdmin extends Admin
{
    /**
     * @var array
     */
    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by'    => 'id',
        '_sort_order' => 'DESC',
    ];

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'genre.fields.ID',
            ])
            ->addIdentifier('origName', null, [
                'label' => 'book.fields.name',
            ])
            ->add('type', null, [
                'label' => 'book.fields.type',
                'editable'  => true,
            ])
            ->add('isActive', null, [
                'label' => 'book.fields.is_active',
                'editable'  => true,
            ])
            ->add('createdAt', null, [
                'label' => 'book.fields.created_at',
                'pattern' => 'eeee, dd MMMM yyyy, HH:mm',
            ]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('origName', null, [
                'label' => 'book.fields.title',
            ])
            ->add('type', null, [
                'label' => 'book.fields.type',
            ])
            ->add('isActive', null, [
                'label' => 'book.fields.is_active',
            ])
            ->add('createdAt', null, [
                'label' => 'book.fields.created_at',
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('book.basic', ['class' => 'col-md-8', 'name' => null])
                ->add('origName', TextType::class, [
                    'label' => 'book.fields.origName',
                ])
                ->add('type', ChoiceType::class, [
                    'label' => 'book.fields.type',
                    'choices' => $this->getType(),
                    'required' => true,
                ])
                ->add('file', VichFileType::class, [
                    'label'             => 'book.fields.file',
                    'required'           => false,
                    'help' => $this->getSubject()->getBookFile() ?: false,
                ])
            ->end()
            ->with('book.additional', ['class' => 'col-md-4', 'name' => null])
                ->add('isActive', null, [
                    'label' => 'book.fields.is_active',
                    'required' => false,
                ])
                ->add('updatedAt', DateTimePickerType::class, [
                    'label'     => 'book.fields.created_by',
                    'required' => true,
                    'format' => 'YYYY-MM-dd HH:mm',
                    'attr' => ['readonly' => true],
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label'     => 'book.fields.created_by',
                    'required' => true,
                    'format' => 'YYYY-MM-dd HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end()
        ;
    }

    /**
     * @return mixed
     */
    private function getType()
    {
        $bookFileEntity = $this->getClass();

        return array_flip($bookFileEntity::getTypeList());
    }
}
