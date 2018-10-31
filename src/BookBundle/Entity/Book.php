<?php

namespace BookBundle\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class Book
 *
 * @ORM\Entity()
 * @ORM\Table(name="books")
 * @ORM\HasLifecycleCallbacks
 *
 * @Vich\Uploadable
 */
class Book
{
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
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $poster;

    /**
     * Unmapped property to handle file uploads
     *
     * @Vich\UploadableField(mapping="book_image", fileNameProperty="poster")
     */
    private $file;

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
     * @var int
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
     * @var int
     *
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    protected $pages;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isActive;

    /**
     * @var int
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="BookBundle\Entity\BookHasFile",
     *     mappedBy="book", cascade={"all"}, orphanRemoval=true
     * )
     * @ORM\OrderBy({"orderNum" = "ASC"})
     */
    protected $bookHasFiles;

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
        $this->download = 0;
        $this->createdAt = $this->updatedAt = new \DateTime('now');
        $this->genres = new ArrayCollection();
        $this->bookHasFiles = new ArrayCollection();
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
     * Set poster
     *
     * @param string $poster
     *
     * @return Book
     */
    public function setPoster($poster = null)
    {
        $this->poster = $poster;

        return $this;
    }

    /**
     * Get image
     *
     * @return integer
     */
    public function getPoster()
    {
        return $this->poster;
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
     *
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
     * Add bookHasFiles.
     *
     * @param \BookBundle\Entity\BookHasFile $bookHasFiles
     *
     * @return Book
     */
    public function addBookHasFile(\BookBundle\Entity\BookHasFile $bookHasFiles)
    {
        $bookHasFiles->setBook($this);
        $this->bookHasFiles[] = $bookHasFiles;

        return $this;
    }

    /**
     * Remove bookHasFiles.
     *
     * @param \BookBundle\Entity\BookHasFile $bookHasFiles
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBookHasFile(\BookBundle\Entity\BookHasFile $bookHasFiles)
    {
        return $this->bookHasFiles->removeElement($bookHasFiles);
    }

    /**
     * Get articleHasAuthors.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBookHasFiles()
    {
        return $this->bookHasFiles;
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
}