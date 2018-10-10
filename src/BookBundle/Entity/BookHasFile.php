<?php

namespace BookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class BookHasFile
 *
 * @ORM\Entity()
 * @ORM\Table(name="books_has_file")
 * @ORM\HasLifecycleCallbacks
 *
 * @Vich\Uploadable
 */
class BookHasFile
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
     * @var \BookBundle\Entity\Book
     *
     * @ORM\ManyToOne(targetEntity="BookBundle\Entity\Book", inversedBy="bookHasFiles")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id", nullable=false)
     */
    protected $book;

    /**
     * @var \BookBundle\Entity\BookFile
     *
     * @ORM\ManyToOne(targetEntity="BookBundle\Entity\BookFile", fetch="EAGER")
     * @ORM\JoinColumn(name="book_file_id", referencedColumnName="id", nullable=false)
     */
    protected $bookFile;

    /**
     * @var int
     *
     * @ORM\Column(name="order_num", type="integer", nullable=false, options={"default": 1})
     */
    protected $orderNum;

    /**
     * Constructor
     */
    public function __construct()
    {
//        if (empty($this->orderNum)) {
//            $this->orderNum = 0;
//        }
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->book;
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
     * Set orderNum.
     *
     * @param int $orderNum
     *
     * @return BookHasFile
     */
    public function setOrderNum($orderNum)
    {
        $this->orderNum = $orderNum;

        return $this;
    }

    /**
     * Get orderNum.
     *
     * @return int
     */
    public function getOrderNum()
    {
        return $this->orderNum;
    }

    /**
     * Set book.
     *
     * @param \BookBundle\Entity\Book $book
     *
     * @return BookHasFile
     */
    public function setBook(\BookBundle\Entity\Book $book)
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Get book.
     *
     * @return \BookBundle\Entity\Book
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * Set bookFile.
     *
     * @param \BookBundle\Entity\BookFile $bookFile
     *
     * @return BookHasFile
     */
    public function setBookFile(\BookBundle\Entity\BookFile $bookFile)
    {
        $this->bookFile = $bookFile;

        return $this;
    }

    /**
     * Get bookFile.
     *
     * @return \BookBundle\Entity\BookFile
     */
    public function getBookFile()
    {
        return $this->bookFile;
    }
}
