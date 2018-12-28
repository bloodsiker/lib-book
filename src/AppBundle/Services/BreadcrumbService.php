<?php
/**
 * Created by PhpStorm.
 * User: ovsiichuk
 * Date: 28.12.18
 * Time: 18:04
 */

namespace AppBundle\Services;

use Symfony\Component\Routing\Router;

/**
 * Class BreadcrumbService
 */
class BreadcrumbService
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var array $breadcrumb
     */
    private $breadcrumb;

    /**
     * BreadcrumbService constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->init();
    }

    /**
     * Init breadcrumb
     */
    public function init()
    {
        $this->addBreadcrumb([
            'title' => 'Главная',
            'href' => $this->router->generate('index'),
        ]);
    }

    /**
     * @param array $array
     *
     * @return $this
     */
    public function addBreadcrumb($array)
    {
        $this->breadcrumb[] = $array;

        return $this;
    }

    /**
     * @return array
     */
    public function getBreadcrumb()
    {
        return $this->breadcrumb;
    }
}