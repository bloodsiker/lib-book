<?php

namespace AdminBundle\Helper;

/**
 * Class SpellCheckHelper
 * @package AdminBundle\Helper
 */
class SpellCheckHelper
{
    const URL = 'http://speller.yandex.net/services/spellservice/checkTexts';
    const IGNORE_UPPERCASE = 1;
    const IGNORE_DIGITS = 2; // ignore words with digits
    const IGNORE_URLS = 4;
    const FIND_REPEAT_WORDS = 8;
    const IGNORE_LATIN = 16;
    const NO_SUGGEST = 32;
    const FLAG_LATIN = 128;
    const IGNORE_CAPITALIZATION = 512;

    static private $instance = null;

    private function __construct()
    {
        /* ... @return Singleton */
    }

    /**
     * @return null|static
     */
    public static function getInstance()
    {
        return
            self::$instance === null
                ? self::$instance = new static()
                : self::$instance;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\ParameterBag $request
     * @return array
     */
    public function check($request)
    {
        switch ($request->get('action')) {
            case 'get_incorrect_words':
                $response = $this->getIncorrectWords($request->get('text'), $request->get('lang'));
                break;
            case 'get_suggestions':
                $response = $this->getSuggestions($request->get('word'), $request->get('lang'));
                break;
            default:
                $response = array();
                break;
        }

        return $response;

    }

    /**
     * @param array  $texts
     * @param string $lang
     * @return \StdClass
     */
    private function getIncorrectWords($texts, $lang)
    {
        $xml = $this->checkTexts($texts, $lang, (self::IGNORE_DIGITS + self::IGNORE_URLS + self::NO_SUGGEST));
        if (!$xml || !$xml->SpellResult) {
            return array();
        }

        $data = array();
        foreach ($xml->SpellResult as $result) {
            $words = array();
            foreach ($result->error as $error) {
                $words[] = (string) $error->word[0];
            }
            $data[] = $words;
        }

        $response = new \StdClass();
        $response->outcome = 'success';
        $response->data = $data;

        return $response;
    }

    /**
     * @param string $word
     * @param string $lang
     * @return array
     */
    private function getSuggestions($word, $lang)
    {
        $xml = $this->checkTexts(array($word), $lang);
        if (!$xml or !$xml->SpellResult) {
            return array();
        }

        $suggestions = array();
        $result = $xml->SpellResult[0];
        foreach ($result->error as $error) {
            foreach ($error->s as $s) {
                $suggestions[] = (string) $s;
            }
            break;
        }

        return $suggestions;
    }

    /**
     * @param array  $texts
     * @param string $lang
     * @param int    $options
     * @return mixed
     */
    private function checkTexts($texts, $lang, $options = 0)
    {
        $body  = 'lang='.urlencode($lang);
        $body .= '&options='.$options;
        foreach ($texts as $text) {
            $body .= "&text=".urlencode($text);
        }

        if (!function_exists('curl_init')) {
            exit('Curl is not available');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $xmlResponse = curl_exec($ch);
        curl_close($ch);

        try {
            $response = simplexml_load_string($xmlResponse);
        } catch (\Exception $e) {
            $response = null;
        }

        return $response;
    }

    private function __clone()
    {
        /* ... @return Singleton */
    }
    private function __wakeup()
    {
        /* ... @return Singleton */
    }
}
