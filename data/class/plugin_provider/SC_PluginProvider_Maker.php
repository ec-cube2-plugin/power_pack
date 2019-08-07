<?php

/**
 * Class SC_PluginProvider_Maker
 *
 * 商品詳細でメーカーの情報を取得可能に
 */
class SC_PluginProvider_Maker extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        $objHelperPlugin->addAction("LC_Page_Products_Detail_action_after", function (LC_Page_Products_Detail $objPage) {
            $objQuery = SC_Query_Ex::getSingletonInstance();
            $objPage->arrMaker = $objQuery->getRow('*', 'dtb_maker', 'maker_id = ?', array($objPage->arrProduct['maker_id']));
        }, $priority);
    }
}
