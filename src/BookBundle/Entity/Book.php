<?php

namespace BookBundle\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Book
 *
 * @ORM\Entity()
 * @ORM\Table(name="books")
 * @ORM\HasLifecycleCallbacks
 */
class Book
{
    /**
     * @var integer
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
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $image;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    protected $description;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    protected $year;

    /**
     * @var \AuthorBundle\Entity\Author
     *
     * @ORM\ManyToOne(targetEntity="AuthorBundle\Entity\Author")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $author;

    /**
     * @var \SeriesBundle\Entity\Series
     *
     * @ORM\ManyToOne(targetEntity="SeriesBundle\Entity\Series")
     * @ORM\JoinColumn(name="series_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $series;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    protected $pages;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $fileFb2;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $fileEpub;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $fileRtf;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $fileDjvu;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $filePdf;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isActive;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $download;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="GenreBundle\Entity\Genre")
     * @ORM\JoinTable(name="books_genres",
     *     joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id", onDelete="CASCADE")}
     *     )
     */
    protected $genres;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isActive = true;
        $this->download = 0;
        $this->createdAt = new \DateTime('now');
        $this->genres = new ArrayCollection();
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (is_null($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->getName());
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->prePersist();
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
     * @param string $name
     *
     * @return Book
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set image
     *
     * @param UploadedFile $image
     *
     * @return Book
     */
    public function setImage(UploadedFile $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return integer
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Book
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Book
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set year
     *
     * @param string $year
     *
     * @return Book
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set pages
     *
     * @param string $pages
     *
     * @return Book
     */
    public function setPages($pages)
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Get pages
     *
     * @return integer
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Set fileFb2
     *
     * @param UploadedFile $fileFb2
     *
     * @return Book
     */
    public function setFileFb2(UploadedFile $fileFb2 = null)
    {
        $this->fileFb2 = $fileFb2;

        return $this;
    }

    /**
     * Get fileFb2
     *
     * @return integer
     */
    public function getFileFb2()
    {
        return $this->fileFb2;
    }

    /**
     * Set fileFb2
     *
     * @param UploadedFile $fileEpub
     *
     * @return Book
     */
    public function setFileEpub(UploadedFile $fileEpub = null)
    {
        $this->fileEpub = $fileEpub;

        return $this;
    }

    /**
     * Get fileEpub
     *
     * @return integer
     */
    public function getFileEpub()
    {
        return $this->fileEpub;
    }

    /**
     * Set fileRtf
     *
     * @param UploadedFile $fileRtf
     *
     * @return Book
     */
    public function setFileRtf(UploadedFile $fileRtf = null)
    {
        $this->fileRtf = $fileRtf;

        return $this;
    }

    /**
     * Get fileRtf
     *
     * @return integer
     */
    public function getFileRtf()
    {
        return $this->fileRtf;
    }

    /**
     * Set fileDjvu
     *
     * @param UploadedFile $fileDjvu
     *
     * @return Book
     */
    public function setFileDjvu(UploadedFile $fileDjvu = null)
    {
        $this->fileDjvu = $fileDjvu;

        return $this;
    }

    /**
     * Get fileDjvu
     *
     * @return integer
     */
    public function getFileDjvu()
    {
        return $this->fileDjvu;
    }

    /**
     * Set filePdf
     *
     * @param UploadedFile $filePdf
     *
     * @return Book
     */
    public function setFilePdf(UploadedFile $filePdf = null)
    {
        $this->filePdf = $filePdf;

        return $this;
    }

    /**
     * Get filePdf
     *
     * @return integer
     */
    public function getFilePdf()
    {
        return $this->filePdf;
    }

    /**
     * Set author
     *
     * @param \AuthorBundle\Entity\Author $author
     *
     * @return Book
     */
    public function setAuthor(\AuthorBundle\Entity\Author $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \AuthorBundle\Entity\Author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set series
     *
     * @param \SeriesBundle\Entity\Series $series
     *
     * @return Book
     */
    public function setSeries(\SeriesBundle\Entity\Series $series = null)
    {
        $this->series = $series;

        return $this;
    }

    /**
     * Get series
     *
     * @return \SeriesBundle\Entity\Series
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Book
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
     * Set download
     *
     * @param boolean $download
     *
     * @return Book
     */
    public function setDownload($download)
    {
        $this->download = $download;

        return $this;
    }

    /**
     * Get download
     *
     * @return integer
     */
    public function getDownload()
    {
        return $this->download;
    }

    /**
     * Add genres
     *
     * @param \GenreBundle\Entity\Genre $genres
     * @return Book
     */
    public function addGenre(\GenreBundle\Entity\Genre $genres)
    {
        $this->genres[] = $genres;

        return $this;
    }

    /**
     * Remove genres
     *
     * @param \GenreBundle\Entity\Genre $genres
     */
    public function removeGenre(\GenreBundle\Entity\Genre $genres)
    {
        $this->genres->removeElement($genres);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Book
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
}
