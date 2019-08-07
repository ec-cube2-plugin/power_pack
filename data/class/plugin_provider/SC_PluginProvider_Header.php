<?php

/**
 * Class SC_PluginProvider_Header
 *
 * ヘッダー表示を改善するための Util
 */
class SC_PluginProvider_Header extends SC_PluginProvider_Base
{
    public function install($arrPlugin, SC_Plugin_Installer $objPluginInstaller = null)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $sql = <<<__EOF__
        CREATE TABLE plg_PowerPack_mtb_hidden_header (
            id SMALLINT,
            name TEXT,
            rank SMALLINT NOT NULL DEFAULT 0,
            PRIMARY KEY (id)
        );
__EOF__;
        $objQuery->query($sql);
    }

    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        $provider = $this;
        $objHelperPlugin->addAction('LC_Page_process', function (LC_Page $objPage) use ($provider) {
            // ヘッダーがいらない場合を判断
            if (!property_exists($objPage, 'hidden_header')) {
                $masterData = new SC_DB_MasterData_Ex();
                $arrHiddenHeader = $masterData->getMasterData('plg_PowerPack_mtb_hidden_header');
                $objPage->hidden_header = false;
                foreach ($arrHiddenHeader as $url) {
                    if (strpos($_SERVER['SCRIPT_NAME'], ROOT_URLPATH . ltrim($url, '/')) === 0) {
                        $objPage->hidden_header = true;
                        break;
                    }
                }
            }

            // 都道府県
            if (!property_exists($objPage, 'arrPref')) {
                $masterData = new SC_DB_MasterData_Ex();
                $objPage->arrPref = $masterData->getMasterData('mtb_pref');
            }

            // login
            if (!property_exists($objPage, 'tpl_login') || ($objPage->tpl_login && (!$objPage->tpl_name1 || $objPage->tpl_name2))) {
                $objCustomer = new SC_Customer_Ex();
                if ($objCustomer->isLoginSuccess()) {
                    $objPage->tpl_login = true;
                    $objPage->tpl_user_point = $objCustomer->getValue('point');
                    $objPage->tpl_name1 = $objCustomer->getValue('name01');
                    $objPage->tpl_name2 = $objCustomer->getValue('name02');
                }
            }

            // cart
            if (!property_exists($objPage, 'arrCartList')) {
                $objCart = new SC_CartSession_Ex();
                $objPage->arrCartList = array(0 => $provider->lfGetCartData($objPage, $objCart));
            }

            // favorite
            if (!property_exists($objPage, 'favorite_count')) {
                $objCustomer = new SC_Customer_Ex();
                if ($objCustomer->isLoginSuccess()) {
                    $objPage->favorite_count = $provider->lfGetFavoriteProductCount($objCustomer->getValue('customer_id'));
                }
            }
        }, $priority);
    }


    /**
     * お気に入りを取得する
     *
     * @param mixed $customer_id
     * @access private
     * @return array お気に入り商品一覧
     */
    public function lfGetFavoriteProductCount($customer_id)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $where = 'f.customer_id = ? AND p.status = 1 AND p.del_flg = 0';
        if (NOSTOCK_HIDDEN) {
            $where .= ' AND EXISTS(SELECT * FROM dtb_products_class WHERE product_id = f.product_id AND del_flg = 0 AND (stock >= 1 OR stock_unlimited = 1))';
        }

        return $objQuery->count(
            'dtb_customer_favorite_products f INNER JOIN dtb_products p USING (product_id)',
            $where,
            array($customer_id)
        );
    }

    /**
     * カートの情報を取得する
     *
     * @param  SC_CartSession $objCart カートセッション管理クラス
     * @return array          カートデータ配列
     */
    public function lfGetCartData($objPage, $objCart)
    {
        $arrCartKeys = $objCart->getKeys();
        $products_total = 0;
        $total_quantity = 0;
        foreach ($arrCartKeys as $cart_key) {
            // 購入金額合計
            $products_total += $objCart->getAllProductsTotal($cart_key);
            // 合計数量
            $total_quantity += $objCart->getTotalQuantity($cart_key);

            // 送料無料チェック
            if (!$this->isMultiple && !$this->hasDownload) {
                $is_deliv_free = $objCart->isDelivFree($cart_key);
            }
        }

        $arrCartList = array();

        $arrCartList['ProductsTotal'] = $products_total;
        $arrCartList['TotalQuantity'] = $total_quantity;

        // 店舗情報の取得
        $arrInfo = SC_Helper_DB_Ex::sfGetBasisData();
        $arrCartList['free_rule'] = $arrInfo['free_rule'];

        // 送料無料までの金額
        if ($is_deliv_free) {
            $arrCartList['deliv_free'] = 0;
        } else {
            $deliv_free = $arrInfo['free_rule'] - $products_total;
            $arrCartList['deliv_free'] = $deliv_free;
        }

        return $arrCartList;
    }
}
