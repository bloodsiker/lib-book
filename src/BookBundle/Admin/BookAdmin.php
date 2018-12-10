<?php

namespace BookBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use AdminBundle\Form\Type\UploadVichImageType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Valid;

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

    /**
     * @return array
     */
    public function getFormTheme()
    {
        return array_merge(
            parent::getFormTheme(),
            ['BookBundle:Form:admin_fields.html.twig']
        );
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'book.fields.id',
            ])
            ->add('poster', null, array(
                'label'     => 'book.fields.poster',
                'template'  => 'BookBundle:Admin:list_image.html.twig',
            ))
            ->addIdentifier('name', null, [
                'label' => 'book.fields.name',
            ])
            ->add('files', null, [
                'label' => 'book.fields.files',
                'template'  => 'BookBundle:Admin:list_image.html.twig',
            ])
            ->add('isActive', null, [
                'label' => 'book.fields.is_active',
                'editable'  => true,
            ])
            ->add('createdAt', null, [
                'label' => 'book.fields.created_at',
                'pattern' => 'eeee, dd MMMM yyyy, HH:mm',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'preview' => ['template' => 'BookBundle:CRUD:list__action_preview.html.twig'],
                    'edit' => [],
                ],
            ]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, [
                'label' => 'book.fields.name',
            ])
            ->add('author', null, [
                'label' => 'book.fields.author',
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
        $context = $this->getPersistentParameter('context');

        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => null])
                ->add('name', TextType::class, [
                    'label' => 'book.fields.name',
                ])
                ->add('description', CKEditorType::class, [
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
                ->add('bookHasFiles', CollectionType::class, [
                    'label' => 'book.fields.files',
                    'required' => false,
                    'constraints' => new Valid(),
                    'by_reference' => false,
                ], [
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'orderNum',
                    'link_parameters' => ['context' => $context],
                    'admin_code' => 'sonata.admin.book_has_files',
                ])
                ->add('bookHasRelated', CollectionType::class, [
                    'label' => 'book.fields.book_related',
                    'required' => false,
                    'constraints' => new Valid(),
                    'by_reference' => false,
                ], [
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'orderNum',
                    'link_parameters' => ['context' => $context],
                    'admin_code' => 'sonata.admin.book_has_related',
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => null])
                ->add('isActive', null, [
                    'label' => 'book.fields.is_active',
                    'required' => false,
                ])
                ->add('poster', ModelListType::class, [
                    'label' => 'book.fields.poster',
                    'required' => false,
                ])
                ->add('author', ModelListType::class, [
                    'label' => 'book.fields.author',
                    'required' => true,
                ])
                ->add('series', ModelListType::class, [
                    'label' => 'book.fields.series',
                    'required' => false,
                ])
                ->add('pages', IntegerType::class, [
                    'label' => 'book.fields.pages',
                    'required' => false,
                ])
                ->add('year', IntegerType::class, [
                    'label' => 'book.fields.year',
                    'required' => false,
                ])
                ->add('genres', ModelAutocompleteType::class, [
                    'label' => 'book.fields.genres',
                    'required' => false,
                    'property' => 'name',
                    'multiple' => true,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('download', IntegerType::class, [
                    'label' => 'book.fields.download',
                    'required' => false,
                    'attr' => ['readonly' => true],
                ])
                ->add('updatedAt', DateTimePickerType::class, [
                    'label'     => 'book.fields.updated_at',
                    'required' => true,
                    'format' => 'dd-MM-YYYY HH:mm',
                    'attr' => ['readonly' => true],
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label'     => 'book.fields.created_at',
                    'required' => true,
                    'format' => 'dd-MM-YYYY HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end()
        ;
    }
}
