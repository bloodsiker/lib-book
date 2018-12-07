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
 * Class MediaFileAdmin
 */
class MediaFileAdmin extends Admin
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
     * @return mixed
     */
    public function getAdminUser()
    {
        return $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('security.token_storage')
            ->getToken()
            ->getUser();
    }

    /**
     * @return mixed
     */
    public function getNewInstance()
    {
        $instance = parent::getNewInstance();
        $instance->setCreatedBy(
            $this->getAdminUser()
        );

        return $instance;
    }

    /**
     * @param MediaFile $object
     */
    public function prePersist($object)
    {
        $this->preUpdate($object);
    }

    /**
     * @param MediaFile $object
     */
    public function preUpdate($object)
    {
        $file = $object->getFile();
        if ($file) {
            $object->setSize($file->getSize());
            $object->setMimeType($file->getMimeType());
        }
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'media_file.fields.id',
            ])
            ->addIdentifier('origName', null, [
                'label' => 'media_file.fields.orig_name',
            ])
            ->addIdentifier('path', null, [
                'label'     => 'media.fields.path',
//                'template'  => 'MediaBundle:Admin:list_media.html.twig',
                'sortable'  => false,
            ])
            ->add('size', null, [
                'label' => 'media_file.fields.size',
            ])
            ->add('isActive', null, [
                'label' => 'media_file.fields.is_active',
                'editable'  => true,
            ])
            ->add('createdAt', null, [
                'label' => 'media_file.fields.created_at',
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
                'label' => 'media_file.fields.orig_name',
            ])
            ->add('isActive', null, [
                'label' => 'media_file.fields.is_active',
            ])
            ->add('createdAt', null, [
                'label' => 'media_file.fields.created_at',
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-4', 'label' => false])
                ->add('origName', TextType::class, [
                    'label' => 'media_file.fields.orig_name',
                ])
                ->add('file', VichFileType::class, [
                    'label'     => 'media_file.fields.file',
                    'required'  => false,
                    'help'      => $this->getSubject()->getPath() ?: false,
                ])
                ->add('createdBy', ModelListType::class, [
                    'label'    => 'media_file.fields.created_by',
                    'btn_edit' => false,
                    'btn_add' => false,
                    'required' => true,
                ])
            ->end()
            ->with('form_group.basic2', ['class' => 'col-md-4', 'label' => false])
                ->add('mimeType', TextType::class, [
                    'label' => 'media_file.fields.mime_type',
                    'required' => false,
                    'attr'  => ['readonly' => true],
                ])
                ->add('size', TextType::class, [
                    'label' => 'media_file.fields.size',
                    'required' => false,
                    'attr'  => ['readonly' => true],
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'label' => false])
                ->add('isActive', null, [
                    'label'    => 'media_file.fields.is_active',
                    'required' => false,
                ])
                ->add('updatedAt', DateTimePickerType::class, [
                    'label'  => 'media_file.fields.updated_at',
                    'format' => 'YYYY-MM-dd HH:mm',
                    'attr'   => ['readonly' => true],
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label'    => 'media_file.fields.created_at',
                    'required' => true,
                    'format'   => 'YYYY-MM-dd HH:mm',
                    'attr'     => ['readonly' => true],
                ])
            ->end();
    }
}
