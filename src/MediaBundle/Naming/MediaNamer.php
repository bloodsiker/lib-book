<?php

namespace MediaBundle\Naming;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Util\Transliterator;

/**
 * Class MediaNamer
 */
class MediaNamer implements NamerInterface
{
    /**
     * image path
     *
     * @var $pathImage
     */
    private $pathImage = null;

    /**
     * @param object          $object
     * @param PropertyMapping $mapping
     *
     * @return string
     */
    public function name($object, PropertyMapping $mapping): string
    {
        $file = $mapping->getFile($object);
        $name = $file->getClientOriginalName();
        $name = uniqid().'_'.Transliterator::transliterate($name);

        return $this->fillPath($object, $name);
    }

    /**
     * @param string $pathImage
     *
     * @return $this
     */
    public function setPathImage(string $pathImage)
    {
        $this->pathImage = $pathImage;

        return $this;
    }

    /**
     * @param object $object
     * @param string $name
     *
     * @return string
     */
    protected function fillPath($object, $name): string
    {
//        $name = uniqid().'.'.$object->getFile()->guessExtension();

        if (null !== $this->pathImage) {
            $pattern = ['[ID/100]', '[ID]', '[FILE]'];
            $replace = [
                intval($object->getId() / 100),
                $object->getId(),
                $name,
            ];
            $path = str_replace($pattern, $replace, $this->pathImage);
        } else {
            $path = '/img_tmp/'.$name;
        }

        return $path;
    }
}