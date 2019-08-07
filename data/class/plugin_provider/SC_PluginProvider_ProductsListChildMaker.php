<?php

/**
 * Class SC_PluginProvider_ProductsListChildMaker
 *
 * 商品一覧で子メーカーの表示を可能に
 */
class SC_PluginProvider_ProductsListChildMaker extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        $provider = $this;
        $objHelperPlugin->addAction("LC_Page_Products_List_action_after", function (LC_Page_Products_List $objPage) use ($provider) {
            if ($objPage->arrSearchData['category_id']) {
                // 選択されたカテゴリーID
                $objQuery = SC_Query_Ex::getSingletonInstance();
                $objPage->arrCategory = $objQuery->getRow("*", 'dtb_category', 'category_id = ?', array($objPage->arrSearchData['category_id']));

                $child_categories = SC_Helper_DB_Ex::sfGetChildrenArray("dtb_category", "parent_category_id", "category_id", $objPage->arrSearchData['category_id']);
                $objPage->arrChildMakers = $provider->lfGetMakerListByCategoryIds($child_categories);
            }
        }, $priority);
    }

    /**
     * メーカーの取得をカテゴリIDから行う.
     *
     * @param integer[] $arrCategoryIds カテゴリID
     * @return array カテゴリツリーの配列
     */
    public function lfGetMakerListByCategoryIds($arrCategoryIds)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->setOption("ORDER BY m.name ASC");
        $arrMakers = $objQuery->select(
            'm.maker_id, m.name',
            'dtb_maker AS m',
            "m.maker_id IN ("
            . "SELECT DISTINCT p.maker_id "
            . "FROM dtb_products AS p "
            . "LEFT JOIN dtb_product_categories pc ON p.product_id = pc.product_id "
            . "WHERE p.del_flg = 0 AND p.status= 1 AND pc.category_id IN (" . SC_Utils_Ex::repeatStrWithSeparator('?', count($arrCategoryIds)) . ')'
            . ') AND m.del_flg = 0',
            $arrCategoryIds
        );

        return $arrMakers;
    }
}
