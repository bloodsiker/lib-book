<?php

namespace PageBundle\Entity;

use Sonata\PageBundle\Entity\BaseSite as BaseSite;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Site
 * @package PageBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="page_site")
 */
class Site extends BaseSite
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
