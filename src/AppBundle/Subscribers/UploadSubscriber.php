<?php

namespace AppBundle\Subscribers;

use AppBundle\Entity\Article;
use AppBundle\Services\FileUploader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadSubscriber implements EventSubscriber
{

    private $uploadFile;

    public function __construct(FileUploader $uploadFile)
    {
        $this->uploadFile = $uploadFile;
    }

    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
            'postPersist',
            'postUpdate',
            'postRemove'
        );
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->preUploadFile($entity);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        die('preUpdate');
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Article && null != $entity->getImage()->getFile()) {
            $this->uploadFile($entity->getImage()->getFile(), $entity->getImage()->getAlt());
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        die('postUpdate');
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Article && null != $entity->getImage()->getAlt()) {
            $this->removeFile($entity->getImage()->getAlt());
        }
    }

    public function preUploadFile($entity)
    {
        if ($entity instanceof Article && null != $entity->getImage()->getFile()) {
            $alt = $this->uploadFile->preUploadImage($entity->getImage()->getFile());
            $entity->getImage()->setAlt($alt);
        }
        return;
    }

    public function uploadFile($file, $imageName)
    {
        if ($file instanceof UploadedFile) {
            $this->uploadFile->uploaderImage($file, $imageName);
        }
        return;
    }

    public function removeFile($imageName)
    {
        $this->uploadFile->removeImage($imageName);
    }
}
