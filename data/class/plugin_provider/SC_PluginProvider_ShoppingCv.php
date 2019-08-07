<?php

/**
 * Class SC_PluginProvider_ShoppingCv
 *
 * 商品コンバージョンタグを簡単に
 */
class SC_PluginProvider_ShoppingCv extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        $objHelperPlugin->addAction("LC_Page_Shopping_Complete_action_before", array($this, "LC_Page_Shopping_Complete_action_before"), $priority);
    }

    public function LC_Page_Shopping_Complete_action_before(LC_Page_Shopping_Complete $objPage)
    {
        // CV用
        $objPurchase = new SC_Helper_Purchase();
        $objPage->arrOrder = $objPurchase->getOrder($_SESSION['order_id']);
        $objPage->arrOrderDetails = $objPurchase->getOrderDetail($_SESSION['order_id']);
    }
}
