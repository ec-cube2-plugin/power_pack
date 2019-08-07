<?php

/**
 * Class SC_PluginProvider_Https
 *
 * Httpsの場合に全てをHttpsに
 */
class SC_PluginProvider_Https extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        PowerPack::addOutputfilter(DEVICE_TYPE_PC, '', array($this, 'filterHttps'));
        PowerPack::addOutputfilter(DEVICE_TYPE_SMARTPHONE, '', array($this, 'filterHttps'));
        PowerPack::addOutputfilter(DEVICE_TYPE_MOBILE, '', array($this, 'filterHttps'));

        PowerPack::addAction('SC_Response::sendRedirect', function (&$location, &$arrQueryString, &$inheritQueryString, &$useSsl) {
            if (HTTP_URL != HTTPS_URL) {
                $useSsl = true;
            }
        });
    }

    public function filterHttps(&$source)
    {
        if (HTTP_URL != HTTPS_URL) {
            $source = str_replace(HTTP_URL, HTTPS_URL, $source);
        }
    }
}
