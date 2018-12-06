<?php

namespace AppBundle\Block;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use MediaBundle\Admin\MediaImageAdmin;
use MediaBundle\Entity\MediaImage;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Form\Type\ImmutableArrayType;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Sonata\CoreBundle\Model\Metadata;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;

/**
 * Class AnnouncementBlockService
 */
class AnnouncementBlockService extends AbstractAdminBlockService
{
    /**
     * Constants
     */
    const COUNT_ITEM = 4;

    /**
     * @var MediaImageAdmin|null
     */
    protected $admin;

    /**
     * @var Registry $doctrine
     */
    private $doctrine;

    /**
     * QuizListBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param Registry        $doctrine
     */
    public function __construct($name, EngineInterface $templating, Registry $doctrine)
    {
        parent::__construct($name, $templating);

        $this->doctrine = $doctrine;
    }

    /**
     * @param mixed $admin
     */
    public function setAdmin($admin = null)
    {
        $this->admin = $admin;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => 'AppBundle:Block:announce.html.twig',
        ]);

        $resolver->setDefaults(
            $this->getDefaultSettings()
        );
    }

    /**
     * @param null $code
     *
     * @return Metadata
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata(
            $this->getName(),
            (!is_null($code) ? $code : $this->getName()),
            false,
            'AppBundle',
            ['class' => 'fa fa-bullhorn']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', ImmutableArrayType::class, [
            'translation_domain' => 'AppBundle',
            'label' => false,
            'keys' => $this->buildDefaultForm($formMapper),
        ]);
    }

    /**
     * @param BlockInterface $block
     */
    public function prePersist(BlockInterface $block)
    {
        for ($i = 1; $i <= self::COUNT_ITEM; $i++) {
            $image = $block->getSetting('image_'.$i);
            $block->setSetting('image_'.$i, is_object($image) ? $image->getId() : $image);
        }
    }

    /**
     * @param BlockInterface $block
     */
    public function preUpdate(BlockInterface $block)
    {
        $this->prePersist($block);
    }

    /**
     * @param BlockInterface $block
     */
    public function load(BlockInterface $block)
    {
        for ($i = 1; $i <= self::COUNT_ITEM; $i++) {
            $image = $block->getSetting('image_'.$i);

            if (!is_object($image) && null !== $image) {
                if ($this->hasAdmin()) {
                    $image = $this->admin->getObject($image);
                } else {
                    $image = $this->doctrine->getRepository(MediaImage::class)->find($image);
                }
            }

            $block->setSetting('image_'.$i, $image);
        }
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $block = $blockContext->getBlock();

        if (!$block->getEnabled()) {
            return new Response();
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'block'    => $blockContext->getBlock(),
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }

    /**
     * @return array
     */
    protected function getDefaultSettings()
    {
        $fields = [];
        for ($i = 1; $i <= self::COUNT_ITEM; $i++) {
            $fields['title_'.$i] = null;
            $fields['link_'.$i] = null;
            $fields['image_'.$i] = null;
        }

        return $fields;
    }

    /**
     * @param FormMapper $formMapper
     *
     * @return array
     */
    protected function buildDefaultForm(FormMapper $formMapper)
    {
        $array = [];
        for ($i = 1; $i <= self::COUNT_ITEM; $i++) {
            $array = array_merge($array, $this->getFormFields($formMapper, $i));
        }

        return $array;
    }

    /**
     * @param FormMapper $formMapper
     * @param int        $i
     *
     * @return array
     */
    protected function getFormFields(FormMapper $formMapper, $i)
    {
        return [
            [$this->getImageAdminBuilder($formMapper, 'image_'.$i), null, [
                'required' => false,
            ], ],
            ['title_'.$i, TextType::class, [
                'label'     => 'app.block.fields.title_announce',
                'required'  => false,
            ], ],
            ['link_'.$i, TextType::class, [
                'label'     => 'app.block.fields.link',
                'required'  => false,
            ], ],
        ];
    }

    /**
     * @param FormMapper $formMapper
     * @param string     $fieldName
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    protected function getImageAdminBuilder(FormMapper $formMapper, string $fieldName)
    {
        if (!$this->hasAdmin()) {
            return $formMapper->create($fieldName, TextType::class, [
                'label'     => 'app.block.fields.image',
                'required'  => true,
            ]);
        }

        $fieldDescription = $this
            ->admin
            ->getModelManager()
            ->getNewFieldDescriptionInstance($this->admin->getClass(), $fieldName);

        $fieldDescription->setAssociationAdmin($this->admin);
        $fieldDescription->setAdmin($formMapper->getAdmin());
        $fieldDescription->setAssociationMapping([
            'fieldName' => $fieldName,
            'type' => ClassMetadataInfo::ONE_TO_MANY,
        ]);
        $fieldDescription->setOption('translation_domain', 'AppBundle');

        return $formMapper->create($fieldName, ModelListType::class, [
            'sonata_field_description'  => $fieldDescription,
            'btn_edit'                  => false,
            'btn_add'                   => false,
            'class'                     => $this->admin->getClass(),
            'model_manager'             => $this->admin->getModelManager(),
            'label'                     => 'app.block.fields.image',
            'required'                  => false,
        ]);
    }

    /**
     * @return bool
     */
    protected function hasAdmin()
    {
        return (null !== $this->admin);
    }
}
