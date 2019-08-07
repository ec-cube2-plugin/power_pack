<?php

/**
 * Class SC_PluginProvider_ProductsRequireLogin
 *
 * 会員限定商品の対応を可能に
 */
class SC_PluginProvider_ProductsRequireLogin extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        $objHelperPlugin->addAction("LC_Page_Products_Detail_action_after", function (LC_Page_Products_Detail $objPage) {
            $objCustomer = new SC_Customer_Ex();
            if ($objPage->arrProduct['require_login'] && !$objCustomer->isLoginSuccess()) {
                $objPage->tpl_mainpage = 'mypage/login.tpl';
                $objPage->tpl_title = $objPage->arrProduct['name'] . ' (会員限定商品)';
            }
        }, $priority);
    }
}
