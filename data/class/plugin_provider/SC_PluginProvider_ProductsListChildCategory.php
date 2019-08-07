<?php

/**
 * Class SC_PluginProvider_ProductsListChildCategory
 *
 * 商品一覧で子カテゴリの表示を可能に
 */
class SC_PluginProvider_ProductsListChildCategory extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        $objHelperPlugin->addAction("LC_Page_Products_List_action_after", function (LC_Page_Products_List $objPage) {
            if ($objPage->arrSearchData['category_id']) {
                $objQuery = SC_Query_Ex::getSingletonInstance();
                $objQuery->setOrder('rank DESC');
                $objPage->arrChildCategories = $objQuery->select('dtb_category.*', 'dtb_category', 'dtb_category.parent_category_id = ?', $objPage->arrSearchData['category_id']);
            }
        }, $priority);
    }
}
