<?php

namespace UploaderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MainBundle\Traits\TimestampableTrait;

/**
 * File
 *
 * @ORM\Table(name="media_file")
 * @ORM\Entity(repositoryClass="UploaderBundle\Repository\MediaFileRepository")
 */
class MediaFile
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="filename", type="string")
     */
    private $filename;

    /**
     * @var string
     * @ORM\Column(name="file_url", type="string")
     */
    private $fileUrl;

    /**
     * @var string
     * @ORM\Column(name="mime_type", type="string")
     */
    private $mimeType;

    use TimestampableTrait;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getFileUrl()
    {
        return $this->fileUrl;
    }

    /**
     * @param string $fileUrl
     */
    public function setFileUrl($fileUrl)
    {
        $this->fileUrl = $fileUrl;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

}
