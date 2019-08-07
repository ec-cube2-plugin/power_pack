<?php

/**
 * Class SC_PluginProvider_Cdn
 *
 * CDN対応
 */
class SC_PluginProvider_Cdn extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        PowerPack::addOutputfilter(DEVICE_TYPE_PC, '', array($this, 'filterCdn'));
        PowerPack::addOutputfilter(DEVICE_TYPE_SMARTPHONE, '', array($this, 'filterCdn'));
        PowerPack::addOutputfilter(DEVICE_TYPE_MOBILE, '', array($this, 'filterCdn'));
    }

    public function filterCdn(&$source)
    {
        if (defined('CDN_URLPATH') && CDN_URLPATH && $_SERVER['HTTP_USER_AGENT'] != 'Amazon CloudFront') {
            $source = preg_replace(array(
                '/(<img[^>]* src=")\/' . ROOT_DIR . '([^>]+\.(?:jpe?g|gif|png))("[^>]*>)/',
                '/(<img[^>]* src=\')\/' . ROOT_DIR . '([^>]+\.(?:jpe?g|gif|png))(\'[^>]*>)/',
                '/(<img[^>]* src=")\/' . ROOT_DIR . '(resize_image\d?\.php?[^>]+)("[^>]*>)/',
                '/(<img[^>]* src=\')\/' . ROOT_DIR . '(resize_image\d?\.php?[^>]+)(\'[^>]*>)/',
                '/(<script[^>]* src=")\/' . ROOT_DIR . '([^>]+\.js)("[^>]*>)/',
                '/(<link[^>]* href=")\/' . ROOT_DIR . '([^>]+\.css)("[^>]*>)/',
            ), '$1' . CDN_URLPATH . '$2$3', $source);
        }
    }
}
