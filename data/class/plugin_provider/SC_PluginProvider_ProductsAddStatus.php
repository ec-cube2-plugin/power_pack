<?php

/**
 * Class SC_PluginProvider_ProductsAddStatus
 *
 * 
 */
class SC_PluginProvider_ProductsAddStatus extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        $objHelperPlugin->addAction("LC_Page_Products_Detail_action_after", function (LC_Page_Products_Detail $objPage) {
            $arrProductId = array();
            foreach ($objPage->arrRecommend as $arrProduct) {
                $arrProductId[] = $arrProduct['product_id'];
            }
            $objProduct = new SC_Product_Ex();
            $objPage->productStatus = $objPage->productStatus + $objProduct->getProductStatus($arrProductId);
        }, $priority);

        $objHelperPlugin->addAction("LC_Page_Mypage_Favorite_action_after", function (LC_Page_Mypage_Favorite $objPage) {
            $masterData = new SC_DB_MasterData_Ex();
            $objPage->arrSTATUS = $masterData->getMasterData('mtb_status');
            $objPage->arrSTATUS_IMAGE = $masterData->getMasterData('mtb_status_image');

            $arrProductId = array();
            foreach ($objPage->arrFavorite as $arrProduct) {
                $arrProductId[] = $arrProduct['product_id'];
            }

            $objProduct = new SC_Product_Ex();
            $objPage->productStatus = $objProduct->getProductStatus($arrProductId);
        }, $priority);

        $objHelperPlugin->addAction("LC_Page_FrontParts_Bloc_Recommend_action_after", function (LC_Page_FrontParts_Bloc_Recommend $objPage) {
            $masterData = new SC_DB_MasterData_Ex();
            $objPage->arrSTATUS = $masterData->getMasterData('mtb_status');
            $objPage->arrSTATUS_IMAGE = $masterData->getMasterData('mtb_status_image');

            $arrProductId = array();
            foreach ($objPage->arrBestProducts as $arrProduct) {
                $arrProductId[] = $arrProduct['product_id'];
            }

            $objProduct = new SC_Product_Ex();
            $objPage->productStatus = $objProduct->getProductStatus($arrProductId);
        }, $priority);
    }
}
