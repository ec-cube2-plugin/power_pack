<?php

/**
 * Class SC_PluginProvider_Ssl
 *
 * ニュースの一覧・詳細
 */
class SC_PluginProvider_News extends SC_PluginProvider_Base
{
    public function install($arrPlugin, SC_Plugin_Installer $objPluginInstaller = null)
    {
        // News
        PowerPack::insertPage(array(
            'page_name'     => '新着情報(一覧)',
            'url'           => 'news/index.php',
            'filename'      => 'news/index',
        ));
        PowerPack::insertPage(array(
            'page_name'     => '新着情報(詳細)',
            'url'           => 'news/detail.php',
            'filename'      => 'news/detail',
        ));
    }
}
