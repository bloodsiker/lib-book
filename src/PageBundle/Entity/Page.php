<?php

namespace PageBundle\Entity;

use Sonata\PageBundle\Entity\BasePage as BasePage;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Page
 * @package PageBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="page_page")
 */
class Page extends BasePage
{
    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Get id.
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }
}
