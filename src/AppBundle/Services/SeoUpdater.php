<?php

namespace AppBundle\Services;

use ArticleBundle\Entity\Article;
use ArticleBundle\Entity\ArticleCategory;
use ArticleBundle\Entity\ArticleSpectopic;
use ArticleBundle\Helper\ArticleRouterHelper;
use DossierBundle\Entity\Dossier;
use Sonata\PageBundle\CmsManager\CmsManagerSelectorInterface;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\SiteInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Cmf\Component\Routing\ChainRouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class SeoUpdater
 */
class SeoUpdater
{
    /**
     * @var CmsManagerSelectorInterface
     */
    private $pageSelector;

    /**
     * @var PageInterface
     */
    private $currentPage;

    /**
     * @var SeoPageInterface
     */
    private $seoPage;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $siteTitle;

    /**
     * @var ChainRouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $routeLocales;

    /**
     * @var SaveStateValue
     */
    private $saveStateService;

    /**
     * @var string
     */
    private $defaultOgImageSrc = '/bundles/app/img/logo_sc.gif';

    /**
     * SeoUpdater constructor.
     * @param CmsManagerSelectorInterface $pageSelector
     * @param SeoPageInterface            $seoPage
     * @param RequestStack                $requestStack
     * @param TranslatorInterface         $translator
     * @param Router                      $router
     * @param SaveStateValue              $saveStateService
     * @param string                      $routeLocales
     */
    public function __construct(CmsManagerSelectorInterface $pageSelector, SeoPageInterface $seoPage, RequestStack $requestStack, TranslatorInterface $translator, Router $router, SaveStateValue $saveStateService, $routeLocales)
    {
        $this->pageSelector = $pageSelector;
        $this->seoPage = $seoPage;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->router = $router;
        $this->routeLocales = $routeLocales;
        $this->saveStateService = $saveStateService;
    }

    /**
     * @param null  $object
     * @param array $params
     *
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function doMagic($object = null, array $params = [])
    {
        $locale = $this->getRequest()->getLocale();
        $this->currentPage = $this->pageSelector->retrieve()->getCurrentPage();

        if (!$this->currentPage) {
            return;
        }

        $this->currentPage->setcurrentLocale($locale);

        $customRouteParams = $params['custom_route_params'] ?? [];
        switch (true) {
            /** @var Article $object */
            case ($object instanceof Article):
                $this->updateArticleMetadata($object, $params);
                break;
            /** @var Dossier $object */
            case ($object instanceof Dossier):
                $this->updateDossierMetadata($object, $params);
                break;
            case ($object instanceof ArticleCategory):
                $this->updateArticleCategoryMetadata($object, $params);
                break;
            case ($object instanceof ArticleSpectopic):
                $this->updateArticleSpectopicMetadata($object, $params);
                break;
            default:
                $this->updateGeneralMetadata($params);
        }

        $request = $this->requestStack->getCurrentRequest();
        $defaultOgImage = $request->getSchemeAndHttpHost().$this->defaultOgImageSrc;


        $this->setLangAlternate($customRouteParams);
        $this->setOpenGraph($params, $defaultOgImage);
        $this->setCanonicalUrl($params, $customRouteParams);
        $this->setOtherMeta($params);
    }

    /**
     * @param array $params
     */
    public function setOtherMeta($params = [])
    {
        $metaData = $this->seoPage->getMetas();
        if (isset($metaData['name']) && isset($metaData['name']['keywords'])) {
            foreach ($metaData['name']['keywords'] as $keywords) {
                if ($keywords) {
                    $this->seoPage->addMeta('name', 'news_keywords', $keywords);
                }
            }
        }

        if (isset($metaData['name']) && !isset($metaData['name']['twitter:card'])) {
            $this->seoPage->addMeta('name', 'twitter:card', 'summary');
        }

        if (isset($params['twitter']) && is_array($params['twitter'])) {
            foreach ($params['twitter'] as $pName => $pVal) {
                $this->seoPage->addMeta('name', $pName, $pVal);
            }
        }

        if (isset($params['page_number']) && $params['page_number']) {
            $this->seoPage->addMeta('name', 'ROBOTS', 'NOINDEX, FOLLOW');
        } else {
            $this->seoPage->addMeta('name', 'ROBOTS', 'INDEX, FOLLOW, ALL');
        }
    }

    /**
     * @return array
     */
    public function getAllMeta()
    {
        return $this->seoPage->getMetas();
    }

    /**
     * @param array $variables
     */
    public function setShortcodeVariables(array $variables)
    {
        $this->shortcodeVariable = $variables;
    }

    /**
     * @return string
     */
    public function getSiteTitle()
    {
        return $this->siteTitle ?: $this->translator->trans('app.frontend.title', [], 'AppBundle');
    }

    /**
     * @param array $params
     *
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function updateGeneralMetadata(array &$params = [])
    {
        /** @var SiteInterface $site */
        $site = $this->currentPage->getSite();

        if (isset($params['title'])) {
            $this->seoPage->setTitle($params['title']);
        } else {
            $this->seoPage->setTitle($this->currentPage->getTitle() ?: ($site->getTitle() ?: $site->getName()));
        }

        if (isset($params['description'])) {
            $this->seoPage->addMeta('name', 'description', $params['description']);
        } else {
            $this->seoPage->addMeta('name', 'description', $this->currentPage->getMetaDescription() ?: ($site->getMetaDescription() ?: $this->translator->trans('app.frontend.meta.description', [], 'AppBundle')));
        }

        if (isset($params['keywords'])) {
            $this->seoPage->addMeta('name', 'keywords', $params['keywords']);
        } else {
            $this->seoPage->addMeta('name', 'keywords', $this->currentPage->getMetaKeyword() ?: ($site->getMetaKeywords() ?: $this->translator->trans('app.frontend.meta.keywords', [], 'AppBundle')));
        }
    }

    /**
     * @param Article $article
     * @param array   $params
     *
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function updateArticleMetadata(Article $article, array &$params = [])
    {
        // если Title задан из админки: выводим то что задано
        if ($title = $article->getMetaTitle()) {
            $title = $this->prepareShortcdeStr($title);
            $title = $this->shortcode->renderShortcodes($title);
            $title .= ' | '.$this->translator->trans('app.frontend.meta.sitename', [], 'AppBundle');

            $this->seoPage->setTitle($title);
        } elseif ($title = $article->getTitle()) {
            if ($article->getCategory() && $article->getCategory()->getTitleHead()) {
                $title .= ' - '.$article->getCategory()->getTitleHead();
            }
            if ($article->getHeader()) {
                $title .= ' - '.$article->getHeader();
            }
            $title .= ' | '.$this->translator->trans('app.frontend.meta.sitename', [], 'AppBundle');

            $this->seoPage->setTitle($title);
        } elseif (isset($params['title'])) {
            $title = '';
            if ($article->getCategory() && $article->getCategory()->getTitle()) {
                $title .= $article->getCategory()->getTitle().' | ';
            }
            $title .= $this->translator->trans('app.frontend.meta.sitename', [], 'AppBundle');

            $this->seoPage->setTitle($title);
        }

        if ($description = $article->getMetaDescription()) {
            $description = $this->prepareShortcdeStr($description);
            $description = $this->shortcode->renderShortcodes($description);
            $description .= ' | '.$this->translator->trans('app.frontend.meta.sitename', [], 'AppBundle');
            $this->seoPage->addMeta('name', 'description', $description);
        } elseif (isset($params['description'])) {
            $this->seoPage->addMeta('name', 'description', $params['description']);
        } else {
            $description = $this->cleanMetaString(mb_substr(strip_tags($article->getDescription()), 0, 150)).'...';
            $this->seoPage->addMeta('name', 'description', $description);
        }

        $keywords = '';
        if ($article->getBlogger()) {
            $keywords .= $article->getBlogger()->getName().', ';
        }

        if ($keywordsMeta = $article->getMetaKeywords()) {
            $keywordsMeta = $this->prepareShortcdeStr($keywordsMeta);
            $keywordsMeta = $this->shortcode->renderShortcodes($keywordsMeta);
            $keywords .= $keywordsMeta;
            $this->seoPage->addMeta('name', 'keywords', $keywords);
        } elseif (isset($params['keywords'])) {
            $this->seoPage->addMeta('name', 'keywords', $keywords.$params['keywords']);
        } elseif ($article->getTags()) {
            foreach ($article->getTags() as $tag) {
                $keywords .= $tag->getName().', ';
            }
            $keywords = mb_substr($keywords, 0, -2);
            $this->seoPage->addMeta('name', 'keywords', $keywords);
        } else {
            $this->seoPage->addMeta('name', 'keywords', $keywords.$this->translator->trans('app.frontend.meta.keywords', [], 'AppBundle'));
        }

        $ogImage = null;
        if ($article->getOgImage()) {
            $path = $article->getOgImage()->getPath();
            if ($this->getRequest()->get('skin') === 'lifestyle') {
                $ogImage = $this->imagineHelper->getFilterImage($path, 'image_650x434');
                $params['og']['og:image:width'] = 650;
                $params['og']['og:image:height'] = 434;
            } else {
                $ogImage = $this->imagineHelper->getFilterImage($path, 'image_610x343');
                $params['og']['og:image:width'] = 610;
                $params['og']['og:image:height'] = 343;
            }

            $customMetaTags = $this->saveStateService->getValue('custom_meta_tags') ?: [];
            $customMetaTags[] = '<link rel="image_src" href="'.$ogImage.'" />';
            $this->saveStateService->setValue('custom_meta_tags', $customMetaTags);

        } elseif ($article->getImage()) {
            $path = $article->getImage()->getPath();
            if ($this->getRequest()->get('skin') === 'lifestyle') {
                $ogImage = $this->imagineHelper->getActualImage($path, 'image_650x434', ['ls', 'main_new', 'main']);
            } else {
                $ogImage = $this->imagineHelper->getActualImage($path, 'image_610x343', ['main_new', 'main']);
            }
            if ($ogImage) {
                $ogImageSize = @getimagesize($ogImage);
                $params['og']['og:image:width'] = $ogImageSize[0];
                $params['og']['og:image:height'] = $ogImageSize[1];

                $customMetaTags = $this->saveStateService->getValue('custom_meta_tags') ?: [];
                $customMetaTags[] = '<link rel="image_src" href="'.$ogImage.'" />';
                $this->saveStateService->setValue('custom_meta_tags', $customMetaTags);
            }
        }
        if (!$ogImage) {
            if ($this->getRequest()->get('skin') === 'lifestyle') {
                $ogImage = $this->defaultOgImageSrcLifestyle;
            } else {
                $ogImage = $this->defaultOgImageSrc;
            }
            $params['og']['og:image:width'] = 200;
            $params['og']['og:image:height'] = 200;
        }

        $params['og']['og:image'] = $ogImage;

        $this->seoPage->addMeta('name', 'twitter:image', $ogImage);

        if ($article->getArticleHasSpectopics()) {
            $customMetaTags = $this->saveStateService->getValue('custom_meta_tags') ?: [];
            $specTopicsJS = '<script>';
            $specTopicsJS .= 'var specialTopics = [];';
            foreach ($article->getArticleHasSpectopics() as $specTopic) {
                $specTopicsJS .= "specialTopics.push('".$this->articleRouterHelper->getSpectopicPath($specTopic->getSpectopic())."');";
            }
            $specTopicsJS .= '</script>';
            $customMetaTags[] = $specTopicsJS;
            $this->saveStateService->setValue('custom_meta_tags', $customMetaTags);
        }
    }

    /**
     * @param Dossier $dossier
     * @param array   $params
     *
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function updateDossierMetadata(Dossier $dossier, array &$params = [])
    {
        // если Meta Title задан из админки: выводим то что задано
        if ($title = $dossier->getMetaTitle()) {
            $title = $this->prepareShortcdeStr($title);
            $title = $this->shortcode->renderShortcodes($title);
            $this->seoPage->setTitle($title);
        } elseif (isset($params['title'])) {
            $this->seoPage->setTitle($params['title']);
        } else {
            $header = strip_tags($dossier->getHeader());
            $header = explode(".", $header);
            $title = $this->translator->trans('frontend.meta_title_text', ['%TITLE%' => $dossier->getTitle()], 'DossierBundle').' '.$header[0];
            $this->seoPage->setTitle($title);
        }

        if ($description = $dossier->getMetaDescription()) {
            $description = $this->prepareShortcdeStr($description);
            $description = $this->shortcode->renderShortcodes($description);
            $this->seoPage->addMeta('name', 'description', $description);
        } elseif (isset($params['description'])) {
            $this->seoPage->addMeta('name', 'description', $params['description']);
        } else {
            $description = $dossier->getTitle().': '.$this->translator->trans('frontend.meta_description_text', [], 'DossierBundle').' '.$dossier->getTitle();
            $this->seoPage->addMeta('name', 'description', $description);
        }

        if ($keywords = $dossier->getMetaKeywords()) {
            $keywords = $this->prepareShortcdeStr($keywords);
            $keywords = $this->shortcode->renderShortcodes($keywords);
            $this->seoPage->addMeta('name', 'keywords', $keywords);
        } elseif (isset($params['keywords'])) {
            $this->seoPage->addMeta('name', 'keywords', $params['keywords']);
        } else {
            $keywords = explode(" ", $dossier->getTitle());
            $this->seoPage->addMeta('name', 'keywords', $keywords[0].', '.$dossier->getTitle().', '.$this->translator->trans('frontend.meta_keywords', [], 'DossierBundle').' '.$keywords[0]);
        }

        $ogImage = null;
        if ($dossier->getImage()) {
            $ogImage = $this->imagineHelper->getActualImage($dossier->getImage(), 'image_610x343', ['main_new', 'main']);
            if ($ogImage) {
                try {
                    $ogImageSize = getimagesize($ogImage);
                    $params['og']['og:image:width'] = $ogImageSize[0];
                    $params['og']['og:image:height'] = $ogImageSize[1];
                } catch (\Exception $exception) {

                }

                $customMetaTags = $this->saveStateService->getValue('custom_meta_tags') ?: [];
                $customMetaTags[] = '<link rel="image_src" href="'.$ogImage.'" />';
                $this->saveStateService->setValue('custom_meta_tags', $customMetaTags);
            }
        }
        if (!$ogImage) {
            $ogImage = $this->defaultOgImageSrc;
            $params['og']['og:image:width'] = 200;
            $params['og']['og:image:height'] = 200;
        }

        $params['og']['og:image'] = $ogImage;
        $this->seoPage->addMeta('name', 'twitter:image', $ogImage);
    }

    /**
     * @param ArticleCategory $category
     * @param array           $params
     *
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function updateArticleCategoryMetadata(ArticleCategory $category, array &$params = [])
    {
        if (($title = $category->getTitleOf()) && isset($params['archive_date']) && $params['archive_date']) {
            $title .= ' '.$this->translator->trans('front.seo.by', [], 'ArticleBundle').' '.$params['archive_date']->format('d').' '.$this->translator->trans('article.calendar.months_ch.'.($params['archive_date']->format('n') - 1), [], 'ArticleBundle').' '.$params['archive_date']->format('Y').' '.$this->translator->trans('front.seo.by_year', [], 'ArticleBundle');

            $this->seoPage->setTitle($title);
        } elseif ($title = $category->getMetaTitle()) {
            $title = $this->prepareShortcdeStr($title);
            $title = $this->shortcode->renderShortcodes($title);
            if (isset($params['archive_date']) && $params['archive_date']) {
                $title .= ' | '.$this->translator->trans('front.seo.news_by_date', [], 'ArticleBundle').' '.$params['archive_date']->format('d.m.Y');
            }
            if (isset($params['page_number']) && $params['page_number']) {
                $title .= ' - '.$this->translator->trans('app.pager.page', [], 'AppBundle').' '.$params['page_number'];
            }
            $this->seoPage->setTitle($title);
        } elseif ($title = $category->getTitle()) {
            $title .= ' | '.$this->translator->trans('app.frontend.meta.sitename', [], 'AppBundle');
            if (isset($params['archive_date']) && $params['archive_date']) {
                $title .= ' | '.$this->translator->trans('front.seo.news_by_date', [], 'ArticleBundle').' '.$params['archive_date']->format('d.m.Y');
            }
            if (isset($params['page_number']) && $params['page_number']) {
                $title .= ' - '.$this->translator->trans('app.pager.page', [], 'AppBundle').' '.$params['page_number'];
            }

            $this->seoPage->setTitle($title);
        }

        if (($description = $category->getTitleOf()) && isset($params['archive_date']) && $params['archive_date']) {
            $description = $this->translator->trans('front.seo.read_all', [], 'ArticleBundle').' '.lcfirst($description).' '.$this->translator->trans('front.seo.by', [], 'ArticleBundle').' '.$params['archive_date']->format('d').' '.$this->translator->trans('article.calendar.months_ch.'.($params['archive_date']->format('n') - 1), [], 'ArticleBundle').' '.$params['archive_date']->format('Y').' '.$this->translator->trans('front.seo.by_year_full', [], 'ArticleBundle');

            $this->seoPage->addMeta('name', 'description', $description);
        } elseif ($metaDescription = $category->getMetaDescription()) {
            $description = '';
            if (isset($params['page_number']) && $params['page_number']) {
                $description .= $this->translator->trans('app.pager.page', [], 'AppBundle').' '.$params['page_number'].' | ';
            }
            if (isset($params['archive_date']) && $params['archive_date']) {
                $description .= $this->translator->trans('front.seo.news_by_date', [], 'ArticleBundle').' '.$params['archive_date']->format('d.m.Y').'. ';
            }
            $description .= $metaDescription;
            $description = $this->prepareShortcdeStr($description);
            $description = $this->shortcode->renderShortcodes($description);
            $this->seoPage->addMeta('name', 'description', $description);
        } else {
            $description = '';
            if (isset($params['page_number']) && $params['page_number']) {
                $description .= $this->translator->trans('app.pager.page', [], 'AppBundle').' '.$params['page_number'].' | ';
            }
            if (isset($params['archive_date']) && $params['archive_date']) {
                $description .= $this->translator->trans('front.seo.news_by_date', [], 'ArticleBundle').' '.$params['archive_date']->format('d.m.Y').'. ';
            }
            $description .= $this->translator->trans('app.frontend.meta.description', [], 'AppBundle');

            $this->seoPage->addMeta('name', 'description', $description);
        }

        if (($title = $category->getTitleOf()) && isset($params['archive_date']) && $params['archive_date']) {
            $keywords = $this->translator->trans('front.seo.news', [], 'ArticleBundle').', '.$category->getTitle().', '.lcfirst($title).', '.lcfirst($title).' '.$this->translator->trans('article.calendar.month.'.($params['archive_date']->format('n') - 1), [], 'ArticleBundle').', '.lcfirst($title).' '.$params['archive_date']->format('Y').', '.lcfirst($title).' '.$this->translator->trans('front.seo.by', [], 'ArticleBundle').' '.$params['archive_date']->format('d').' '.$this->translator->trans('article.calendar.months_ch.'.($params['archive_date']->format('n') - 1), [], 'ArticleBundle').' '.$params['archive_date']->format('Y');

            $this->seoPage->addMeta('name', 'keywords', $keywords);
        } elseif ($keywords = $category->getMetaKeywords()) {
            $keywords = $this->prepareShortcdeStr($keywords);
            $keywords = $this->shortcode->renderShortcodes($keywords);
            $this->seoPage->addMeta('name', 'keywords', $keywords);
        } else {
            $this->seoPage->addMeta('name', 'keywords', $keywords.$this->translator->trans('app.frontend.meta.keywords', [], 'AppBundle'));
        }

        if ($this->getRequest()->get('skin') === 'lifestyle') {
            $ogImage = $this->defaultOgImageSrcLifestyle;
        } else {
            $ogImage = $this->defaultOgImageSrc;
        }
        $params['og']['og:image:width'] = 200;
        $params['og']['og:image:height'] = 200;
        $params['og']['og:image'] = $ogImage;

        $this->seoPage->addMeta('name', 'twitter:image', $ogImage);
    }

    /**
     * @param ArticleSpectopic $spectopic
     * @param array            $params
     */
    private function updateArticleSpectopicMetadata(ArticleSpectopic $spectopic, array &$params = [])
    {
        $metaTitle = $spectopic->getMetaTitle() ?: $spectopic->getTitle().' | '.$this->translator->trans('app.frontend.meta.sitename', [], 'AppBundle');
        if (isset($params['page_number']) && $params['page_number']) {
            $metaTitle .= ' - '.$this->translator->trans('app.pager.page', [], 'AppBundle').' '.$params['page_number'];
        }
        if (isset($params['archive_date']) && $params['archive_date']) {
            $metaTitle .= ' | '.$this->translator->trans('front.seo.news_by_date', [], 'ArticleBundle').' '.$params['archive_date']->format('d.m.Y');
        }
        $this->seoPage->setTitle($metaTitle);

        $metaDescription = $spectopic->getMetaDescription() ?: $this->translator->trans('front.spectopic.meta_description_text_alternate', [], 'ArticleBundle').': '.$spectopic->getTitle();
        if (isset($params['page_number']) && $params['page_number']) {
            $metaDescription .= $this->translator->trans('app.pager.page', [], 'AppBundle').' '.$params['page_number'].' | ';
        }
        if (isset($params['archive_date']) && $params['archive_date']) {
            $metaDescription .= '. '.$this->translator->trans('front.seo.news_by_date', [], 'ArticleBundle').' '.$params['archive_date']->format('d.m.Y');
        }
        $this->seoPage->addMeta('name', 'description', $metaDescription);

        $metaKeywords = $spectopic->getMetaKeywords() ?: $this->translator->trans('front.spectopic.meta_keywords_text', [], 'ArticleBundle');
        $this->seoPage->addMeta('name', 'keywords', $metaKeywords);

        if ($this->getRequest()->get('skin') === 'lifestyle') {
            $ogImage = $this->defaultOgImageSrcLifestyle;
        } else {
            $ogImage = $this->defaultOgImageSrc;
        }
        $params['og']['og:image:width'] = 200;
        $params['og']['og:image:height'] = 200;
        $params['og']['og:image'] = $ogImage;

        $this->seoPage->addMeta('name', 'twitter:image', $ogImage);
    }

    /**
     * @param array  $params
     * @param string $defaultOgImage
     */
    private function setOpenGraph(array $params, $defaultOgImage)
    {
        if (isset($params['og']) && is_array($params['og'])) {
            $ogParams = $params['og'];
            if (!isset($ogParams['og:image']) || empty($ogParams['og:image'])) {
                $ogParams['og:image'] = $defaultOgImage;
            }

            foreach ($ogParams as $name => $value) {
                $this->seoPage->addMeta('property', $name, $value);
            }
        }
    }

    /**
     * @param array $params
     * @param array $customRouteParams
     */
    private function setCanonicalUrl(array $params, $customRouteParams = [])
    {
        $showCanonicalUrl = !isset($params['showCanonicalUrl'])
            ? true
            : (isset($params['showCanonicalUrl']) && $params['showCanonicalUrl']) ? true : false;

        if (isset($params['canonicalUrl'])) {
            if ($showCanonicalUrl) {
                $this->seoPage->setLinkCanonical($params['canonicalUrl']);
            }
            $this->seoPage->addMeta('property', 'og:url', $params['canonicalUrl']);
        } else {
            $request = $this->getRequest();
            $routeParams = $this->prepareRouteParams($request->attributes->all());
            $routeParams = array_merge($routeParams, $customRouteParams);
            if (isset($routeParams['page'])) {
                $routeParams['page'] = null;
            }
            if ($route = $request->attributes->get('_route')) {
                $url = $this->generateUrlByRoute($route, $routeParams, $request->attributes->get('_locale'));
                if ($showCanonicalUrl) {
                    $this->seoPage->setLinkCanonical($url);
                }
                $this->seoPage->addMeta('property', 'og:url', $url);
            }
        }
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    private function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * @param string      $subject
     * @param string|null $spectopicName
     *
     * @return string
     */
    private function prepareShortcdeStr($subject, $spectopicName = null)
    {
        foreach ($this->shortcodeVariable as $k => $param) {
            $param = stripslashes($param);
            switch (true) {
                case $k === 'site':
                    $subject = str_replace($param, sprintf('%s[%s]', $param, $this->getSiteTitle()), $subject);
                    break;
                case $k === 'spectopic':
                    $subject = str_replace($param, sprintf('%s[%s]', $param, $spectopicName), $subject);
                    break;
            }
        }

        return $subject;
    }

    /**
     * add language alternate in SeoPage for all Locales
     *
     * @param array $customRouteParams
     */
    private function setLangAlternate($customRouteParams = [])
    {
        $request = $this->getRequest();
        $routeParams = $this->prepareRouteParams($request->attributes->all());
        $routeParams = array_merge($routeParams, $customRouteParams);
        foreach ($this->getLocales() as $locale) {
            $url = $this->generateUrlByRoute($request->attributes->get('_route'), $routeParams, $locale);
            $this->seoPage->addLangAlternate($url, str_replace('ua', 'uk', $locale));
        }
    }

    /**
     * @param array $routeParams
     *
     * @return array
     */
    private function prepareRouteParams(array $routeParams)
    {
        $request = $this->getRequest();
        $resultParams = (array) $request->query->all();
        foreach ($routeParams as $k => $param) {
            if (is_string($param) && preg_match('/^[^_].*$/', $k)) {
                $resultParams[$k] = $param;
            }
        }

        return $resultParams;
    }

    /**
     * @param string $route
     * @param array  $routeParams
     * @param string $locale
     *
     * @return string
     */
    private function generateUrlByRoute($route, array $routeParams, $locale = null)
    {
        $routeParams['_locale'] = $locale ?: $this->getRequest()->getLocale();

        return $this->router->generate($route, $routeParams, UrlGenerator::ABSOLUTE_URL);
    }

    /**
     * @return array
     */
    private function getLocales()
    {
        return explode('|', $this->routeLocales) ?: [];
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function cleanMetaString($string)
    {
        return preg_replace(['/(\[[^\]]+\])/Sui', '/[\n\r]/'], ['', ''], $string);
    }
}
