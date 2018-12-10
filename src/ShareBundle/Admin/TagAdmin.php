<?php

namespace ShareBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class TagAdmin
 */
class TagAdmin extends Admin
{
    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by'      => 'id',
        '_sort_order'   => 'DESC',
    ];

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('share.form_group.basic', array('class' => 'col-md-8', 'label' => false))
                ->add('name', TextType::class, [
                    'label' => 'tag.fields.name',
                    'required' => true,
                ])
                ->add('metaTitle', TextType::class, [
                    'label' => 'tag.fields.name',
                    'required' => true,
                ])
                ->add('metaDescription', TextareaType::class, [
                    'label' => 'tag.fields.name',
                    'required' => true,
                    'attr' => ['rows' => 5],
                ])
                ->add('metaKeywords', TextareaType::class, [
                    'label' => 'tag.fields.name',
                    'required' => true,
                    'attr' => ['rows' => 5],
                ])
            ->end()
            ->with('share.form_group.additional', array('class' => 'col-md-4', 'label' => false))
                ->add('slug', TextType::class, array(
                    'label' => 'tag.fields.slug',
                    'required' => false,
                    'attr'      => array(
                        'readonly'  => $this->getSubject()->getId() > 0,
                    ),
                ))
                ->add('isActive', CheckboxType::class, array(
                    'label' => 'tag.fields.is_active',
                    'required' => false,
                ))
            ->end()
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, [
                'label' => 'tag.fields.id',
            ])
            ->add('translations.name', null, array(
                'label' => 'tag.fields.name',
            ))
            ->add('isActive', null, array(
                'label' => 'tag.fields.is_active',
            ))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'tag.fields.id',
            ])
            ->add('isActive', null, array(
                'label' => 'tag.fields.is_active',
            ))
            ->addIdentifier('name', null, array(
                'label' => 'tag.fields.name',
                'field' => 'name',
                'template' => 'AdminBundle::translatable.html.twig',
            ))
            ->add('slug', null, array(
                'label' => 'tag.fields.slug',
            ))
            ->add('_action', 'actions', array(
                'actions' => array('edit' => array()),
            ))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('slug')
            ->add('isActive')
        ;
    }
}
