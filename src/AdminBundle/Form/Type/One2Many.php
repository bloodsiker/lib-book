<?php

namespace AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class One2Many
 * @package AdminBundle\Form\Type
 */
class One2Many extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('fields' => array()));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'one2many';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'sonata_type_collection';
    }
}
