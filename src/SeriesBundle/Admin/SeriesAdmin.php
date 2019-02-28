<?php

namespace SeriesBundle\Admin;

use AdminBundle\Admin\BaseAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class SeriesAdmin
 */
class SeriesAdmin extends Admin
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
     * {@inheritdoc}
     */
    public function setTranslationDomain($translationDomain)
    {
        $this->translationDomain = $translationDomain;
        $this->formOptions['translation_domain'] = $translationDomain;
    }

    /**
     * @return mixed
     */
    public function getNewInstance()
    {
        $instance = parent::getNewInstance();

        $type = (int) $this->getRequest()->get('type');
        if ($this->hasRequest() && !is_null($type)) {
            $instance->setType($type);
        }

        return $instance;
    }

    /**
     * @param string $context
     *
     * @return \Sonata\AdminBundle\Datagrid\ProxyQueryInterface
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery();

        if ('list' === $context) {
            $type = (int) $this->getRequest()->get('type');

            if ($type) {
                $query->andWhere($query->expr()->eq($query->getRootAlias().'.type', ':type'));
                $query->setParameter('type', $type);
            }
        }

        return $query;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'series.fields.id',
            ])
            ->addIdentifier('title', null, [
                'label' => 'series.fields.title',
            ])
            ->add('type', 'choice', [
                'label' => 'series.fields.type',
                'choices' => array_flip($this->getTypes()),
                'catalogue' => $this->getTranslationDomain(),
            ])
            ->add('parent', null, [
                'label' => 'series.fields.parent',
            ])
            ->add('isActive', null, [
                'label' => 'series.fields.is_active',
                'editable'  => true,
            ])
            ->add('createdAt', null, [
                'label' => 'series.fields.created_at',
                'pattern' => 'eeee, dd MMMM yyyy, HH:mm',
            ]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, [
                'label' => 'series.fields.title',
            ])
            ->add('type', null, [
                'label' => 'series.fields.type',
            ], ChoiceType::class, [
                'choices' => $this->getTypes(),
                'choice_translation_domain' => $this->getTranslationDomain(),
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('parent', null, [
                'label' => 'series.fields.parent',
            ])
            ->add('isActive', null, [
                'label' => 'series.fields.is_active',
            ])
            ->add('createdAt', null, [
                'label' => 'series.fields.created_at',
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form_group.basic', ['class' => 'col-md-8', 'name' => false])
                ->add('title', TextType::class, [
                    'label' => 'series.fields.title',
                ])
                ->add('slug', TextType::class, [
                    'label' => 'series.fields.slug',
                    'required' => false,
                    'attr' => ['readonly' => !$this->getSubject()->getId() ? false : true],
                ])
            ->end()
            ->with('form_group.additional', ['class' => 'col-md-4', 'name' => false])
                ->add('isActive', null, [
                    'label' => 'series.fields.is_active',
                    'required' => false,
                ])
                ->add('type', ChoiceType::class, [
                    'label' => 'series.fields.type',
                    'choices' => $this->getTypes(),
                    'required' => true,
                ])
                ->add('parent', ModelListType::class, [
                    'label' => 'series.fields.parent',
                    'required' => false,
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label'     => 'series.fields.created_at',
                    'required' => true,
                    'format' => 'YYYY-MM-dd HH:mm',
                    'attr' => ['readonly' => true],
                ])
            ->end();
    }

    /**
     * @return mixed
     *
     * @throws \Exception
     */
    private function getTypes()
    {
        $entity = $this->getClass();
        $types = $entity::getTypeList();

        foreach ($types as $key => $value) {
            $typeChoice['series.form.types.'.$value] = $key;
        }

        return $typeChoice;
    }
}
