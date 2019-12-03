<?php
/**
 * Created by PhpStorm.
 * User: mandip
 * Date: 2/22/19
 * Time: 5:14 PM
 */

namespace MediaBundle\Listeners;

use Doctrine\ORM\EntityManager;
use MediaBundle\Entity\MediaFile;
use JMS\DiExtraBundle\Annotation as DI;
use Oneup\UploaderBundle\Event\PostPersistEvent;

/**
 * Class UploaderListener
 * @package UploaderBundle\Listeners
 * @DI\Service("app.upload_listener")
 * @DI\Tag("kernel.event_listener", attributes={"event"="oneup_uploader.post_persist", "method"="onUpload"})
 */
class UploaderListener
{

    /**
     * @var EntityManager
     * @DI\Inject("doctrine.orm.entity_manager")
     */
    public $em;

    public function onUpload(PostPersistEvent $event){
        /**
         * @var $uploadedFile \Symfony\Component\HttpFoundation\File\File
         */
        $uploadedFile = $event->getFile();
        $file = new MediaFile();
        $file->setFilename($uploadedFile->getFilename());
        $file->setFileUrl("/uploads/file/".$uploadedFile->getFilename());
        $file->setMimeType($uploadedFile->getMimeType());

        try{
            $this->em->persist($file);
            $this->em->flush();
            $response = [
                'success' => true,
                'id' => $file->getId(),
                'mimeType' => $file->getMimeType(),
                'message' => 'File uploaded.'
            ];
        }   catch (\Throwable $e){
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return $response;
    }

}
