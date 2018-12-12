<?php

namespace CommentBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class CommentAdmin
 */
class CommentAdmin extends Admin
{
    /**
     * @var array
     */
    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by'    => 'id',
        '_sort_order' => 'ASC',
    ];

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'comment.fields.id',
            ])
            ->addIdentifier('book', null, [
                'label' => 'comment.fields.book',
            ])
            ->add('rating', null, [
                'label' => 'comment.fields.rating',
            ])
            ->add('isActive', null, [
                'label' => 'comment.fields.is_active',
                'editable'  => true,
            ])
            ->add('createdAt', null, [
                'label' => 'comment.fields.created_at',
                'pattern' => 'eeee, dd MMMM yyyy, HH:mm',
            ]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('book', null, [
                'label' => 'comment.fields.book',
            ])
            ->add('isActive', null, [
                'label' => 'comment.fields.is_active',
            ])
            ->add('createdAt', null, [
                'label' => 'comment.fields.created_at',
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => false])
                ->add('userName', TextType::class, [
                    'label' => 'comment.fields.name',
                    'required' => false,
                ])
                ->add('userEmail', TextType::class, [
                    'label' => 'comment.fields.email',
                    'required' => false,
                ])
                ->add('comment', TextareaType::class, [
                    'label' => 'comment.fields.comment',
                    'attr' => [
                        'rows' => 5,
                    ],
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => false])
                ->add('isActive', null, [
                    'label' => 'comment.fields.is_active',
                    'required' => false,
                ])
                ->add('rating', IntegerType::class, [
                    'label' => 'comment.fields.rating',
                    'required' => true,
                ])
                ->add('user', ModelListType::class, [
                    'label' => 'comment.fields.created_by',
                    'required' => false,
                ])
                ->add('book', ModelListType::class, [
                    'label' => 'comment.fields.book',
                    'required' => false,
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label'     => 'comment.fields.created_at',
                    'required' => true,
                    'format' => 'YYYY-MM-dd HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end();
    }
}
