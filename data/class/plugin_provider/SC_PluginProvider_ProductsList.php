<?php

/**
 * Class SC_PluginProvider_ProductsList
 *
 * 商品一覧改善
 */
class SC_PluginProvider_ProductsList extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        $objHelperPlugin->addAction("LC_Page_Products_List_action_after", array($this, "LC_Page_Products_List_action_after"), $priority);
    }

    public function LC_Page_Products_List_action_after(LC_Page_Products_List $objPage)
    {
        // 選択された商品ステータスID
        if (!empty($objPage->arrSearchData['product_status_id'])) {
            $objQuery = SC_Query_Ex::getSingletonInstance();
            $objPage->arrProductStatus = $objQuery->getRow("*", 'mtb_status', 'id = ?', array($objPage->arrSearchData['product_status_id']));
        }

        // METAタグ
        if ($objPage->arrCategory) {
            if (!empty($objPage->arrCategory['author'])) {
                $objPage->arrPageLayout['author'] = $objPage->arrCategory['author'];
            }
            if (!empty($objPage->arrCategory['description'])) {
                $objPage->arrPageLayout['description'] = $objPage->arrCategory['description'];
            }
            if (!empty($objPage->arrCategory['keyword'])) {
                $objPage->arrPageLayout['keyword']  = $objPage->arrCategory['keyword'];
            }
        } elseif ($objPage->arrMaker) {
            if (!empty($objPage->arrMaker['author'])) {
                $objPage->arrPageLayout['author'] = $objPage->arrMaker['author'];
            }
            if (!empty($objPage->arrMaker['description'])) {
                $objPage->arrPageLayout['description'] = $objPage->arrMaker['description'];
            }
            if (!empty($objPage->arrMaker['keyword'])) {
                $objPage->arrPageLayout['keyword']  = $objPage->arrMaker['keyword'];
            }
        }
        if ($objPage->arrMaker) {
            if ($objPage->tpl_subtitle == '全商品') {
                $objPage->tpl_subtitle = $objPage->arrMaker['name'];
            } else {
                $objPage->tpl_subtitle .= ' メーカー:' . $objPage->arrMaker['name'];
            }
        }
        if ($objPage->arrProductStatus) {
            $objPage->tpl_subtitle .= ' (' . $objPage->arrProductStatus['name'] . ')';
        }
    }
}
