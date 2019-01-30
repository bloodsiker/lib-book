<?php

namespace SeriesBundle\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Series
 *
 * @ORM\Entity(repositoryClass="SeriesBundle\Entity\SeriesRepository")
 * @ORM\Table(name="series")
 * @ORM\HasLifecycleCallbacks
 */
class Series
{
    const TYPE_AUTHOR = 1;
    const TYPE_PUBLISHING = 2;

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
     * @ORM\Column(type="string", length=150, nullable=false)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $slug;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", length=1, nullable=false)
     */
    protected $type;

    /**
     * @var \SeriesBundle\Entity\Series
     *
     * @ORM\ManyToOne(targetEntity="SeriesBundle\Entity\Series", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="SeriesBundle\Entity\Series", mappedBy="parent")
     */
    protected $children;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isActive;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="BookBundle\Entity\Book", mappedBy="series")
     */
    protected $books;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="BookBundle\Entity\Book", mappedBy="seriesPublishing")
     */
    protected $booksPublishing;

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
        $this->createdAt = new \DateTime('now');
        $this->books = new ArrayCollection();
        $this->booksPublishing = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->title;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (is_null($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->getTitle());
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
     * Set title
     *
     * @param string $title
     *
     * @return Series
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Series
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
     * Set parent
     *
     * @param \SeriesBundle\Entity\Series $parent
     *
     * @return $this
     */
    public function setParent(\SeriesBundle\Entity\Series $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \SeriesBundle\Entity\Series
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children.
     *
     * @param \SeriesBundle\Entity\Series $child
     *
     * @return $this
     */
    public function addChild(\SeriesBundle\Entity\Series $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove children.
     *
     * @param \SeriesBundle\Entity\Series $child
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeChild(\SeriesBundle\Entity\Series $child)
    {
        return $this->children->removeElement($child);
    }

    /**
     * Get children.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Series
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
     * @return Series
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
     * Add book
     *
     * @param \BookBundle\Entity\Book $books
     *
     * @return $this
     */
    public function addBook(\BookBundle\Entity\Book $books)
    {
        $this->books[] = $books;

        return $this;
    }

    /**
     * Remove book
     *
     * @param \BookBundle\Entity\Book $books
     */
    public function removeBook(\BookBundle\Entity\Book $books)
    {
        $this->books->removeElement($books);
    }

    /**
     * Get books
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBooks()
    {
        return $this->books;
    }

    /**
     * Add booksPublishing
     *
     * @param \BookBundle\Entity\Book $books
     *
     * @return $this
     */
    public function addBookPublishing(\BookBundle\Entity\Book $books)
    {
        $this->booksPublishing[] = $books;

        return $this;
    }

    /**
     * Remove booksPublishing
     *
     * @param \BookBundle\Entity\Book $books
     */
    public function removeBookPublishing(\BookBundle\Entity\Book $books)
    {
        $this->booksPublishing->removeElement($books);
    }

    /**
     * Get booksPublishing
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBooksPublishing()
    {
        return $this->booksPublishing;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return $this
     */
    public function setType(int $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array
     */
    public static function getTypeList()
    {
        return [
            self::TYPE_AUTHOR     => 'author',
            self::TYPE_PUBLISHING => 'publishing',
        ];
    }
}
