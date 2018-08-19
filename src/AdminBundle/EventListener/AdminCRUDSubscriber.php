<?php

namespace AdminBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use UploadBundle\Services\UploadManager;
use UploadBundle\Helper\FileFormatHelper;

/**
 * Class AdminCRUDSubscriber
 */
class AdminCRUDSubscriber implements EventSubscriber
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var UploadManager
     */
    private $uploadManager;

    /**
     * @var array
     */
    private $removeList;

    /**
     * @var array
     */
    private $uploadParameters;

    /**
     * Constructor
     *
     * @param Reader        $reader
     * @param UploadManager $uploadManager
     * @param array         $uploadParameters
     */
    public function __construct(Reader $reader, UploadManager $uploadManager, $uploadParameters)
    {
        $this->reader = $reader;
        $this->uploadManager = $uploadManager;
        $this->removeList = array();
        $this->uploadParameters = $uploadParameters;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
            'postPersist',
            'postUpdate',
            'postRemove',
        );
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->fillPathAttributes($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->fillPathAttributes($args);
        $this->fillRemoveList($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->upload($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->upload($args);
        $this->remove();
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($data = $this->getAnnotationData($entity)) {
            foreach ($data as $name => $pathAttribute) {
                $getPath = 'get'.ucfirst($pathAttribute);

                $path = $entity->$getPath();

                if ($path !== null) {
                    $this->uploadManager->delete($path);
                }
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    protected function fillPathAttributes(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($data = $this->getAnnotationData($entity)) {
            foreach ($data as $name => $pathAttribute) {
                $getFile = 'get'.ucfirst($name);
                $setPath = 'set'.ucfirst($pathAttribute);
                if ($entity->$getFile() !== null) {
                    $className = join('', array_slice(explode('\\', get_class($entity)), -1));
                    $entityName = preg_replace('/(?=\p{Lu})/u', '$1-$2', lcfirst($className));
                    $path = $this->uploadManager->buildPathFor(
                        $entity->$getFile(),
                        ((isset($this->uploadParameters[$entityName]['dir_path']) and $this->uploadParameters[$entityName]['dir_path'])
                            ? $this->uploadParameters[$entityName]['dir_path'] : $entityName)
                    );
                    $entity->$setPath($path);
                }
            }
        }
    }

    protected function fillRemoveList(PreUpdateEventArgs $args)
    {
        $this->removeList = array();

        $entity = $args->getEntity();

        if ($data = $this->getAnnotationData($entity)) {
            foreach ($data as $name => $pathAttrribute) {
                if ($args->hasChangedField($pathAttrribute)) {
                    $this->removeList[] = $args->getOldValue($pathAttrribute);
                }
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    protected function upload(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($data = $this->getAnnotationData($entity)) {
            foreach ($data as $name => $pathAttribute) {
                $getFile = 'get'.ucfirst($name);
                $getPath = 'get'.ucfirst($pathAttribute);

                if ($entity->$getFile() !== null) {
                    $file = $entity->$getFile();
                    FileFormatHelper::format($file->getRealPath(), $file->getClientOriginalExtension());
                    $this->uploadManager->saveAs($file, $entity->$getPath());
                }
            }
        }
    }

    /**
     *
     */
    protected function remove()
    {
        foreach ($this->removeList as $file) {
            $this->uploadManager->delete($file);
        }
    }

    /**
     * @param mixed $class
     *
     * @return array
     */
    protected function getAnnotationData($class)
    {
        $data = array();

        $reflectionClass = new \ReflectionClass($class);

        $classAnnotation = $this->reader->getClassAnnotation($reflectionClass, 'UploadBundle\Annotation\HasUploadItems');

        if ($classAnnotation !== null) {
            foreach ($reflectionClass->getProperties() as $reflectionProperty) {
                $annotation = $this->reader->getPropertyAnnotation($reflectionProperty, 'UploadBundle\Annotation\File');

                if ($annotation !== null) {
                    $data[$reflectionProperty->getName()] = $annotation->pathAttribute;
                }
            }
        }

        return $data;
    }
}
