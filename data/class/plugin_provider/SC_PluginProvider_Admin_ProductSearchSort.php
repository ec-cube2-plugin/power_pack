<?php

/**
 * Class SC_PluginProvider_Admin_ProductSearchSort
 *
 * 管理画面＞商品管理 で商品の並び替えを可能にする
 */
class SC_PluginProvider_Admin_ProductSearchSort extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        PowerPack::addPrefilter(DEVICE_TYPE_ADMIN, 'products/index.tpl', function(&$source) {
            $objTransform = new SC_Helper_Transform($source);
            $objTransform->select('p.page_rows', 0)->appendChild('<!--{include file=\'products/_index_orderby.tpl\'}-->');
            $source = $objTransform->getHTML();
        });

        PowerPack::addAction('LC_Page_Admin_Products::init', function (plg_PowerPack_LC_Page_Admin_Products $objPage) {
            $objPage->arrOrderby = array(
                'update_date_desc'  => '更新日時▼',
                'update_date_asc'   => '更新日時▲',
                'product_id_asc'    => '商品ID▲',
                'product_id_desc'   => '商品ID▼',
                'product_code_asc'    => '商品コード▲',
                'product_code_desc'   => '商品コード▼',
                'price02_asc'   => '価格▲',
                'price02_desc'  => '価格▼',
                'stock_asc'   => '在庫▲',
                'stock_desc'  => '在庫▼',
            );
        });

        PowerPack::addFormParam('LC_Page_Admin_Products', function (plg_PowerPack_SC_FormParam $objFormParam) use ($priority) {
            $objFormParam->addParam('表示順序', 'search_orderby', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));

            PowerPack::addAction('LC_Page_Admin_Products::findProducts', function (plg_PowerPack_LC_Page_Admin_Products $objPage, &$where, &$arrValues, &$limit, &$offset, &$order, $objProduct) use ($objFormParam) {
                switch ($objFormParam->getValue('search_orderby')) {
                    case 'update_date_asc':
                        $order = 'update_date DESC';
                        break;
                    case 'update_date_desc':
                        $order = 'update_date DESC';
                        break;
                    case 'product_id_asc':
                        $order = 'product_id ASC';
                        break;
                    case 'product_id_desc':
                        $order = 'product_id DESC';
                        break;
                    case 'product_code_asc':
                        $order = 'product_code_min ASC';
                        break;
                    case 'product_code_desc':
                        $order = 'product_code_max DESC';
                        break;
                    case 'price02_asc':
                        $order = 'price02_min ASC';
                        break;
                    case 'price02_desc':
                        $order = 'price02_max DESC';
                        break;
                    case 'stock_asc':
                        $order = 'stock_unlimited_min ASC, stock_min ASC';
                        break;
                    case 'stock_desc':
                        $order = 'stock_unlimited_min DESC, stock_max DESC';
                        break;
                }
            }, $priority);
        }, $priority);

        PowerPack::addFormParam('LC_Page_Admin_Products_Product', function (plg_PowerPack_SC_FormParam $objFormParam) use ($priority) {
            $objFormParam->addParam('表示順序', 'search_orderby', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        }, $priority);

        PowerPack::addFormParam('LC_Page_Admin_Products_ProductClass', function (plg_PowerPack_SC_FormParam $objFormParam) use ($priority) {
            $objFormParam->addParam('表示順序', 'search_orderby', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        }, $priority);
    }
}
