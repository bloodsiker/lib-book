<?php

namespace OrderBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class OrderBoardAdmin
 */
class OrderBoardAdmin extends Admin
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
                'label' => 'order_board.fields.id',
            ])
            ->addIdentifier('bookTitle', null, [
                'label' => 'order_board.fields.book_title',
            ])
            ->add('status', null, [
                'label' => 'order_board.fields.status',
                'editable'  => true,
            ])
            ->add('vote', null, [
                'label' => 'order_board.fields.vote',
                'editable'  => true,
            ])
            ->add('user', null, [
                'label' => 'order_board.fields.user',
                'editable'  => true,
            ])
            ->add('userName', null, [
                'label' => 'order_board.fields.user_name',
                'editable'  => true,
            ])
            ->add('createdAt', null, [
                'label' => 'order_board.fields.created_at',
                'pattern' => 'eeee, dd MMMM yyyy, HH:mm',
            ]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('bookTitle', null, [
                'label' => 'order_board.fields.book_title',
            ])
            ->add('status', null, [
                'label' => 'order_board.fields.status',
            ])
            ->add('createdAt', null, [
                'label' => 'order_board.fields.created_at',
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => false])
                ->add('bookTitle', TextType::class, [
                    'label' => 'order_board.fields.book_title',
                ])
                ->add('userName', TextType::class, [
                    'label' => 'order_board.fields.user_name',
                    'required' => false,
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => false])
                ->add('user', ModelListType::class, [
                    'label' => 'order_board.fields.user',
                    'required' => false,
                ])
                ->add('status', null, [
                    'label' => 'order_board.fields.status',
                    'required' => false,
                ])
                ->add('vote', IntegerType::class, [
                    'label' => 'order_board.fields.vote',
                    'required' => false,
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label'     => 'order_board.fields.created_at',
                    'required' => true,
                    'format' => 'YYYY-MM-dd HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end();
    }
}
