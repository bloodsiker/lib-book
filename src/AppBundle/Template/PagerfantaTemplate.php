<?php

namespace AppBundle\Template;

use AppBundle\Services\SaveStateValue;
use Pagerfanta\View\Template\DefaultTemplate;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class PagerfantaTemplate
 */
class PagerfantaTemplate extends DefaultTemplate
{
    static protected $defaultOptions = [
        'prev_message'       => '<a class="pagination-prev" href="%href%"><span></span><b>%prev_text%</b></a>',
        'next_message'       => '<a class="pagination-next" href="%href%"><b>%next_text%</b><span></span></a>',
        'css_disabled_class' => '',
        'css_dots_class'     => '',
        'css_current_class'  => 'pagination-current',
        'dots_text'          => '...',
        'container_template' => '<div class="br"></div><div class="pagination">%prev_next%<ul>%pages%</ul></div>',
        'page_template'      => '<li><a href="%href%"%rel%>%text%</a></li>',
        'span_template'      => '<li class="%class%" ><a href="%href%">%text%</a></li>',
        'rel_previous'        => 'prev',
        'rel_next'            => 'next',
    ];

    /**
     * @var AssetExtension
     */
    private $asset;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var SaveStateValue
     */
    private $saveStateService;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param int         $page
     * @param string      $text
     * @param null|string $rel
     *
     * @return mixed|string
     */
    public function pageWithText($page, $text, $rel = null)
    {
        $search = array('%href%', '%text%', '%rel%');

        $href = $this->generateRoute($page > 1 ? '/p'.$page : null);
        $replace = $rel ? array($href, $text, ' rel="'.$rel.'"') : array($href, $text, '');

        return str_replace($search, $replace, $this->option('page_template'));
    }

    /**
     * @param string      $href
     * @param string      $text
     * @param string|null $rel
     *
     * @return mixed
     */
    public function extraPageWithText($href, $text, $rel = null)
    {
        $search = array('%href%', '%text%', '%rel%');

        $replace = $rel ? array($href, $text, ' rel="'.$rel.'"') : array($href, $text, '');

        return str_replace($search, $replace, $this->option('page_template'));
    }

    /**
     * @param int $page
     *
     * @return mixed|string
     */
    public function current($page)
    {
        return $this->generateSpan($this->option('css_current_class'), $page);
    }

    /**
     * @param AssetExtension $asset
     *
     * @return void
     */
    public function setAssets(AssetExtension $asset)
    {
        $this->asset = $asset;
    }

    /**
     * @param TranslatorInterface $translator
     *
     * @return void
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param SaveStateValue $saveStateService
     *
     * @return void
     */
    public function setSaveStateService(SaveStateValue $saveStateService)
    {
        $this->saveStateService = $saveStateService;
    }

    /**
     * @param RequestStack $requestStack
     *
     * @return void
     */
    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return string
     */
    public function previousDisabled()
    {
        return $this->option('prev_message');
    }

    /**
     * @param int $page
     *
     * @return string
     */
    public function previousEnabled($page)
    {
        $route = $this->generateRoute($page > 1 ? '/p'.$page : null);
        if ($route) {
            $customMetaTags = $this->saveStateService->getValue('custom_meta_tags') ?: [];
            $customMetaTags[] = '<link rel="prev" href="'.$this->requestStack->getCurrentRequest()->getScheme().'://'.$this->requestStack->getCurrentRequest()->getHttpHost().$route.'">';
            $this->saveStateService->setValue('custom_meta_tags', $customMetaTags);
        }

        return str_replace(['%href%'], $route, $this->previousDisabled());
    }

    /**
     * @return string
     */
    public function nextDisabled()
    {
        return $this->option('next_message');
    }

    /**
     * @param int $page
     *
     * @return string
     */
    public function nextEnabled($page)
    {
        $route = $this->generateRoute($page > 1 ? '/p'.$page : null);
        if ($route) {
            $customMetaTags = $this->saveStateService->getValue('custom_meta_tags') ?: [];
            $customMetaTags[] = '<link rel="next" href="'.$this->requestStack->getCurrentRequest()->getScheme().'://'.$this->requestStack->getCurrentRequest()->getHttpHost().$route.'">';
            $this->saveStateService->setValue('custom_meta_tags', $customMetaTags);
        }

        return str_replace(['%href%'], $route, $this->nextDisabled());
    }

    /**
     * @param string $domain
     *
     * @return void
     */
    public function init(string $domain)
    {
        $options = [
            'prev_message' => str_replace(
                '%prev_text%',
                $this->translator->trans('app.pager.previous', [], $domain),
                self::$defaultOptions['prev_message']
            ),
            'next_message' => str_replace(
                '%next_text%',
                $this->translator->trans('app.pager.next', [], $domain),
                self::$defaultOptions['next_message']
            ),
        ];

        $this->setOptions($options);
    }

    /**
     * @param string $class
     * @param int    $page
     *
     * @return string
     */
    private function generateSpan($class, $page)
    {
        $search = array('%class%', '%text%', '%href%');
        $replace = array($class, $page, $this->generateRoute($page > 1 ? '/p'.$page : null));

        return str_replace($search, $replace, $this->option('span_template'));
    }
}