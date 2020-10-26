<?php

define("POWERPACK_REVIEW_PMAX", 10);

/**
 * Class SC_PluginProvider_ProductsDetailReviewPage
 *
 * レビューのページ送りを可能に
 */
class SC_PluginProvider_ProductsDetailReviewPage extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        $provider = $this;
        $objHelperPlugin->addAction("LC_Page_Products_Detail_action_after", function (LC_Page_Products_Detail $objPage) use ($provider) {
            // レビュー取得
            $objPage->objReviewNavi = new SC_PageNavi_Ex(
                $objPage->getMode() === 'review' ? $_REQUEST['pageno'] : 1,
                $provider->lfCountReview($objPage->tpl_product_id),
                POWERPACK_REVIEW_PMAX,
                '',
                NAVI_PMAX,
                'product_id=' . urlencode($objPage->tpl_product_id) . '&mode=review&pageno=#page#',
                SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE
            );
            $objPage->arrReviews = $provider->lfGetReview($objPage->tpl_product_id, $objPage->objNavi->start_row);
        }, $priority);
    }

    /**
     * 商品ごとのレビュー情報を取得する
     *
     * @param int $product_id
     * @return int
     */
    public static function lfCountReview($product_id)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();

        return $objQuery->count('dtb_review', 'del_flg = 0 AND status = 1 AND product_id = ?', array($product_id));
    }

    /**
     * 商品ごとのレビュー情報を取得する
     *
     * @param int $product_id
     * @param int $startno
     * @return array
     */
    public static function lfGetReview($product_id, $startno = 0)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->setOrder('create_date DESC');
        $objQuery->setLimit(POWERPACK_REVIEW_PMAX);
        $objQuery->setLimitOffset(POWERPACK_REVIEW_PMAX, $startno);

        return $objQuery->select('*', 'dtb_review', 'del_flg = 0 AND status = 1 AND product_id = ?', array($product_id));
    }
}
