<?php
/**
 * @author         Vladimir Popov
 * @copyright      Copyright (c) 2014 Vladimir Popov
 */

class VladimirPopov_WebForms_Helper_Data
    extends Mage_Core_Helper_Abstract
{

    public function getRealIp()
    {
        $ip = false;

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);

            if ($ip) {
                array_unshift($ips, $ip);
                $ip = false;
            }

            for ($i = 0; $i < count($ips); $i++) {
                if (!preg_match("/^(10|172\.16|192\.168)\./i", $ips[$i])) {
                    if (version_compare(phpversion(), "5.0.0", ">=")) {
                        if (ip2long($ips[$i]) != false) {
                            $ip = $ips[$i];
                            break;
                        }
                    } else {
                        if (ip2long($ips[$i]) != -1) {
                            $ip = $ips[$i];
                            break;
                        }
                    }
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }

    public function captchaAvailable()
    {
        if (class_exists('Zend_Service_ReCaptcha') && Mage::getStoreConfig('webforms/captcha/public_key') && Mage::getStoreConfig('webforms/captcha/private_key'))
            return true;
        return false;
    }

    public function getCaptcha()
    {
        $pubKey = Mage::getStoreConfig('webforms/captcha/public_key');
        $privKey = Mage::getStoreConfig('webforms/captcha/private_key');

        if ($pubKey && $privKey) {
            $recaptcha = Mage::getModel('webforms/captcha');
            $recaptcha->setPublicKey($pubKey);
            $recaptcha->setPrivateKey($privKey);

            $theme = Mage::getStoreConfig('webforms/captcha/theme');

            if ($theme)
                $recaptcha->setOption('theme', $theme);

            $language = Mage::getStoreConfig('webforms/captcha/language');

            if ($language)
                $recaptcha->setOption('lang', $language);
        }
        return $recaptcha;
    }

    public function getMageEdition()
    {
        $version = explode('.', Mage::getVersion());

        if ($version[1] >= 9)
            return 'EE';

        return 'CE';
    }

    public function getMageSubversion()
    {
        $version = explode('.', Mage::getVersion());
        if (!empty($version[1])) return $version[1];
        return false;
    }

    public function htmlCut($text, $max_length)
    {
        $tags = array();
        $result = "";

        $is_open = false;
        $grab_open = false;
        $is_close = false;
        $in_double_quotes = false;
        $in_single_quotes = false;
        $tag = "";

        $i = 0;
        $stripped = 0;

        $stripped_text = strip_tags($text);

        while ($i < strlen($text) && $stripped < strlen($stripped_text) && $stripped < $max_length) {
            $symbol = $text{$i};
            $result .= $symbol;

            switch ($symbol) {
                case '<':
                    $is_open = true;
                    $grab_open = true;
                    break;

                case '"':
                    if ($in_double_quotes)
                        $in_double_quotes = false;
                    else
                        $in_double_quotes = true;

                    break;

                case "'":
                    if ($in_single_quotes)
                        $in_single_quotes = false;
                    else
                        $in_single_quotes = true;

                    break;

                case '/':
                    if ($is_open && !$in_double_quotes && !$in_single_quotes) {
                        $is_close = true;
                        $is_open = false;
                        $grab_open = false;
                    }

                    break;

                case ' ':
                    if ($is_open)
                        $grab_open = false;
                    else
                        $stripped++;

                    break;

                case '>':
                    if ($is_open) {
                        $is_open = false;
                        $grab_open = false;
                        array_push($tags, $tag);
                        $tag = "";
                    } else if ($is_close) {
                        $is_close = false;
                        array_pop($tags);
                        $tag = "";
                    }

                    break;

                default:
                    if ($grab_open || $is_close)
                        $tag .= $symbol;

                    if (!$is_open && !$is_close)
                        $stripped++;
            }

            $i++;
        }

        while ($tags)
            $result .= "</" . array_pop($tags) . ">";

        return $result;
    }

    public function addAssets(Mage_Core_Model_Layout $layout)
    {
        $head = $layout->getBlock('head');
        $content = $layout->getBlock('content');

        if ($head && $content) {

            $head->addCss('webforms/form.css');
            $head->addJs('prototype/window.js');
            $head->addItem('js_css', 'prototype/windows/themes/default.css');
            $head->addItem('js_css', 'prototype/windows/themes/alphacube.css');

        }

        // add custom assets
        Mage::dispatchEvent('webforms_add_assets', array('layout' => $layout));

        return $this;
    }

    public function randomAlphaNum($length = 6)
    {
        $rangeMin = pow(36, $length - 1); //smallest number to give length digits in base 36
        $rangeMax = pow(36, $length) - 1; //largest number to give length digits in base 36

        $base10Rand = @mt_rand($rangeMin, $rangeMax); //get the random number
        if(!$base10Rand)
            $base10Rand = @mt_rand($rangeMax, $rangeMin);
        $newRand = base_convert($base10Rand, 10, 36); //convert it

        return $newRand; //spit it out
    }

    public function getVersion()
    {
        return (string) Mage::getConfig()->getNode()->modules->VladimirPopov_WebForms->version;
    }
}

?>