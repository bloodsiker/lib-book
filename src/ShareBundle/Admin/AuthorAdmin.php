<?php

namespace ShareBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class AuthorAdmin
 */
class AuthorAdmin extends Admin
{
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
                'label' => 'author.fields.id',
            ])
            ->add('photo', null, [
                'label'     => 'author.fields.photo',
                'template'  => 'ShareBundle:Admin:list_fields.html.twig',
            ])
            ->addIdentifier('name', null, [
                'label' => 'author.fields.name',
            ])
            ->add('isAllowDownload', null, [
                'label' => 'author.fields.is_allow_download',
                'editable'  => true,
            ])
            ->add('isActive', null, [
                'label' => 'author.fields.is_active',
                'editable'  => true,
            ])
            ->add('createdAt', null, [
                'label' => 'author.fields.created_at',
            ]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, [
                'label' => 'author.fields.name',
            ])
            ->add('isActive', null, [
                'label' => 'author.fields.is_active',
            ])
            ->add('isAllowDownload', null, [
                'label' => 'author.fields.is_allow_download',
            ])
            ->add('createdAt', null, [
                'label' => 'author.fields.created_at',
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => false])
                ->add('name', TextType::class, [
                    'label' => 'author.fields.name',
                ])
                ->add('slug', TextType::class, [
                    'label' => 'author.fields.slug',
                    'required' => false,
                    'attr' => ['readonly' => !$this->getSubject()->getId() ? false : true],
                ])
                ->add('biography', CKEditorType::class, [
                    'label' => 'author.fields.biography',
                    'config_name' => 'advanced',
                    'required' => false,
                    'attr' => [
                        'rows' => 5,
                    ],
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => false])
                ->add('isActive', null, [
                    'label' => 'author.fields.is_active',
                    'required' => false,
                ])
                ->add('isAllowDownload', null, [
                    'label' => 'author.fields.is_allow_download',
                    'required' => false,
                ])
                ->add('photo', ModelListType::class, [
                    'label' => 'author.fields.photo',
                    'required' => false,
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label'     => 'author.fields.created_at',
                    'required' => true,
                    'format' => 'YYYY-MM-dd HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end();
    }
}
