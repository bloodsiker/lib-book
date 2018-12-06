<?php

namespace MediaBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichFileType;

/**
 * Class MediaAdmin
 */
class MediaAdmin extends Admin
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
                'label' => 'media.fields.id',
            ])
            ->addIdentifier('origName', null, [
                'label' => 'media.fields.orig_name',
            ])
            ->add('isActive', null, [
                'label' => 'media.fields.is_active',
                'editable'  => true,
            ])
            ->add('createdAt', null, [
                'label' => 'media.fields.created_at',
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
                'label' => 'media.fields.orig_name',
            ])
            ->add('isActive', null, [
                'label' => 'media.fields.is_active',
            ])
            ->add('createdAt', null, [
                'label' => 'media.fields.created_at',
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => false])
                ->add('origName', TextType::class, [
                    'label' => 'media.fields.orig_name',
                ])
                ->add('file', VichFileType::class, [
                    'label'     => 'media.fields.file',
                    'required'  => false,
                    'help'      => $this->getSubject()->getMediaFile() ?: false,
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => false])
                ->add('isActive', null, [
                    'label' => 'comment.fields.is_active',
                    'required' => false,
                ])
                ->add('updatedAt', DateTimePickerType::class, [
                    'label'     => 'media.fields.updated_at',
                    'format' => 'YYYY-MM-dd HH:mm',
                    'attr' => ['readonly' => true],
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label'     => 'media.fields.created_at',
                    'required' => true,
                    'format' => 'YYYY-MM-dd HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end();
    }
}
