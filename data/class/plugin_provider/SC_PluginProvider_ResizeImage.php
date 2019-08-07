<?php

/**
 * Class SC_PluginProvider_ResizeImage
 *
 * 画像リサイズを可能に
 */
class SC_PluginProvider_ResizeImage extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        $objHelperPlugin->addAction("LC_Page_ResizeImage_Ex_action_before", function (LC_Page_ResizeImage $objPage)
        {
            $objPage = new plg_PowerPack_LC_Page_ResizeImage();
            $objPage->init();
            $objPage->process();
            exit;
        }, $priority);
    }
}
