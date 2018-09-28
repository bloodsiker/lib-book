<?php

namespace BookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class BookFile
 *
 * @ORM\Entity()
 * @ORM\Table(name="books_file")
 * @ORM\HasLifecycleCallbacks
 *
 * @Vich\Uploadable
 */
class BookFile
{
    const FILE_FB2     = 1;
    const FILE_EPUB    = 2;
    const FILE_DJVU    = 3;
    const FILE_RTF     = 4;
    const FILE_PDF     = 5;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $origName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $bookFile;

    /**
     * Unmapped property to handle file uploads
     *
     * @Vich\UploadableField(mapping="book_file", fileNameProperty="bookFile")
     */
    private $file;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", columnDefinition="TINYINT(1)")
     */
    protected $type;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updatedAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isActive = true;
        $this->createdAt = new \DateTime('now');
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->origName;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $origName
     *
     * @return BookFile
     */
    public function setOrigName($origName)
    {
        $this->origName = $origName;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getOrigName()
    {
        return $this->origName;
    }

    /**
     * Set bookFile
     *
     * @param string $bookFile
     *
     * @return BookFile
     */
    public function setBookFile($bookFile = null)
    {
        $this->bookFile = $bookFile;

        return $this;
    }

    /**
     * Get bookFile
     *
     * @return string
     */
    public function getBookFile()
    {
        return $this->bookFile;
    }

    /**
     * Sets file
     *
     * @param File|null $file
     *
     * @throws \Exception
     */
    public function setFile(File $file = null)
    {
        $this->file = $file;
        if (null !== $file) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return BookFile
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return BookFile
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return BookFile
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Book
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return array
     */
    public static function getTypeList()
    {
        return [
            self::FILE_FB2    => 'FB2',
            self::FILE_EPUB   => 'EPUB',
            self::FILE_DJVU   => 'DJVU',
            self::FILE_RTF    => 'RTF',
            self::FILE_PDF    => 'PDF',
        ];
    }
}
