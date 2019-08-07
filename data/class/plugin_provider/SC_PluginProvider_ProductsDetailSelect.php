<?php

define("POWERPACK_QUANTITY_MAX", 10);

/**
 * Class SC_PluginProvider_ProductsDetailSelect
 *
 * 商品詳細の選択をそれぞれに表示
 */
class SC_PluginProvider_ProductsDetailSelect extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        $objHelperPlugin->addAction("LC_Page_Products_List_action_after", function (LC_Page_Products_List $objPage) {
            $objPage->arrProductClasses = array();
            $objProduct = new SC_Product_Ex();
            foreach ($objPage->arrProducts as $arrProduct) {
                $arrProductClasses = $objProduct->getProductsClassFullByProductId($arrProduct['product_id']);
                foreach ($arrProductClasses as $arrProductClass) {
                    // 税込計算
                    if (!SC_Utils_Ex::isBlank($arrProductClass['price01'])) {
                        $arrProductClass['price01_inctax'] = SC_Helper_TaxRule_Ex::sfCalcIncTax($arrProductClass['price01'], $arrProductClass['product_id'], $arrProductClass['product_class_id']);
                    }
                    if (!SC_Utils_Ex::isBlank($arrProductClass['price02'])) {
                        $arrProductClass['price02_inctax'] = SC_Helper_TaxRule_Ex::sfCalcIncTax($arrProductClass['price02'], $arrProductClass['product_id'], $arrProductClass['product_class_id']);
                    }
                    if (!SC_Utils_Ex::isBlank($arrProductClass['price03'])) {
                        $arrProductClass['price03_inctax'] = SC_Helper_TaxRule_Ex::sfCalcIncTax($arrProductClass['price03'], $arrProductClass['product_id'], $arrProductClass['product_class_id']);
                    }

                    $arrProductClass['quantity'] = array();
                    $sale_limit = $arrProductClass['sale_limit'] ? $arrProductClass['sale_limit'] : POWERPACK_QUANTITY_MAX;
                    $stock = $arrProductClass['stock_unlimited'] ? POWERPACK_QUANTITY_MAX : $arrProductClass['stock'];
                    for ($i = 1; $i <= min($sale_limit, $stock, POWERPACK_QUANTITY_MAX); $i++) {
                        $arrProductClass['quantity'][$i] = $i;
                    }
                    $objPage->arrProductClasses[$arrProductClass['product_id']][] = $arrProductClass;
                }
            }
        }, $priority);

        $objHelperPlugin->addAction("LC_Page_Products_Detail_action_after", function (LC_Page_Products_Detail $objPage) {
            $objPage->arrProductClasses = array();
            $objProduct = new SC_Product_Ex();
            $arrProductClasses = $objProduct->getProductsClassFullByProductId($objPage->tpl_product_id);
            foreach ($arrProductClasses as $arrProductClass) {
                // 税込計算
                if(!SC_Utils_Ex::isBlank($arrProductClass['price01'])) {
                    $arrProductClass['price01_inctax'] = SC_Helper_TaxRule_Ex::sfCalcIncTax($arrProductClass['price01'], $arrProductClass['product_id'], $arrProductClass['product_class_id']);
                }
                if(!SC_Utils_Ex::isBlank($arrProductClass['price02'])) {
                    $arrProductClass['price02_inctax'] = SC_Helper_TaxRule_Ex::sfCalcIncTax($arrProductClass['price02'], $arrProductClass['product_id'], $arrProductClass['product_class_id']);
                }
                if(!SC_Utils_Ex::isBlank($arrProductClass['price03'])) {
                    $arrProductClass['price03_inctax'] = SC_Helper_TaxRule_Ex::sfCalcIncTax($arrProductClass['price03'], $arrProductClass['product_id'], $arrProductClass['product_class_id']);
                }

                $arrProductClass['quantity'] = array();
                $sale_limit = $arrProductClass['sale_limit'] ? $arrProductClass['sale_limit'] : POWERPACK_QUANTITY_MAX;
                $stock = $arrProductClass['stock_unlimited'] ? POWERPACK_QUANTITY_MAX : $arrProductClass['stock'];
                for ($i = 1; $i <= min($sale_limit, $stock, POWERPACK_QUANTITY_MAX); $i++) {
                    $arrProductClass['quantity'][$i] = $i;
                }
                $objPage->arrProductClasses[$arrProductClass['product_id']][] = $arrProductClass;
            }
        }, $priority);
    }
}
