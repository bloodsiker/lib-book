<?php

namespace AppBundle\Twig;

/**
 * Class AppExtension
 */
class AppExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_extension';
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return array(
            new \Twig_SimpleTest('instanceof', array($this, 'isInstanceOf')),
        );
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('is_boolean', array($this, 'isBool')),
            new \Twig_SimpleFunction('is_string', array($this, 'isString')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('file_size_humanize', array($this, 'fileSizeHumanize')),
            new \Twig_SimpleFilter('url_decode', array($this, 'urlDecode')),
        );
    }

    /**
     * Checks if $var is instance of $instance
     *
     * @param mixed $var
     * @param mixed $instance
     *
     * @return bool
     */
    public function isInstanceOf($var, $instance)
    {
        return $var instanceof $instance;
    }

    /**
     * Returns a file size in human readable format.
     *
     * @param integer $size
     *
     * @return string
     */
    public function fileSizeHumanize($size)
    {
        $prefix = array('b', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb');

        $counter = 0;

        while (($size / 1024) > 1) {
            $size = $size / 1024;
            $counter += 1;
        }

        return sprintf('%.2f%s', $size, $prefix[$counter]);
    }

    /**
     * URL Decode a string
     *
     * @param string $url
     *
     * @return string The decoded URL
     */
    public function urlDecode($url)
    {
        return urldecode($url);
    }

    /**
     * You need to check if you were given a boolean value
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isBoolean($value)
    {
        return is_bool($value);
    }

    /**
     * You need to check if you were given a string value
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isString($value)
    {
        return is_string($value);
    }

}
