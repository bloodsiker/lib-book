<?php

namespace BookBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class BookAdmin
 */
class BookAdmin extends Admin
{
    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by'    => 'id',
        '_sort_order' => 'DESC',
    ];

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'genre.fields.ID',
            ])
            ->addIdentifier('name', null, [
                'label' => 'book.fields.name',
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

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, [
                'label' => 'book.fields.title',
            ])
            ->add('isActive', null, [
                'label' => 'book.fields.is_active',
            ])
            ->add('createdAt', null, [
                'label' => 'book.fields.created_at',
            ]);
    }
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('genre.basic', ['class' => 'col-md-4', 'name' => null])
                ->add('name', TextType::class, [
                    'label' => 'book.fields.name',
                ])
                ->add('description', TextareaType::class, [
                    'label' => 'book.fields.description',
                    'attr' => [
                        'rows' => 5,
                    ],
                ])
                ->add('slug', TextType::class, [
                    'label' => 'book.fields.slug',
                    'required' => false,
                    'attr' => ['readonly' => !$this->getSubject()->getId() ? false : true],
                ])
            ->end()
            ->with('genre.second_basic', ['class' => 'col-md-4', 'name' => null])
                ->add('author', ModelListType::class, [
                    'label' => 'book.fields.author',
                    'required' => true,
                ])
                ->add('year', IntegerType::class, [
                    'label' => 'book.fields.year',
                ])
                ->add('genres', ModelAutocompleteType::class, [
                    'label' => 'book.fields.genres',
                    'required' => false,
                    'property' => ['name'],
                    'multiple' => true,
                    'attr' => ['class' => 'form-control'],
                ])
            ->end()
            ->with('genre.additional', ['class' => 'col-md-4', 'name' => null])
                ->add('isActive', null, [
                    'label' => 'book.fields.is_active',
                    'required' => false,
                ])
                ->add('download', IntegerType::class, [
                    'label' => 'book.fields.download',
                    'required' => false,
                    'attr' => ['readonly' => true],
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label'     => 'book.fields.created_by',
                    'required' => true,
                    'format' => 'YYYY-MM-dd HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end();
    }
}
