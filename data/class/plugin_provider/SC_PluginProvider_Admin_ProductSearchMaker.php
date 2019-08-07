<?php

class SC_PluginProvider_Admin_ProductSearchMaker extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        PowerPack::addPrefilter(DEVICE_TYPE_ADMIN, 'products/index.tpl', function(&$source) {
            $objTransform = new SC_Helper_Transform($source);
            $objTransform->select('#search_form table', 0)->appendChild('<!--{include file=\'products/_index_search_brand.tpl\'}-->');
            $source = $objTransform->getHTML();
        });

        PowerPack::addFormParam('LC_Page_Admin_Products', function (plg_PowerPack_SC_FormParam $objFormParam) use ($priority) {
            $objFormParam->addParam('メーカー', 'search_maker_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));

            PowerPack::addAction('LC_Page_Admin_Products::buildQuery', function (plg_PowerPack_LC_Page_Admin_Products $objPage, $key, &$where, &$arrValues, $objFormParam, $objDb) {
                $key2 = preg_replace('/\Asearch_/', '', $key);
                if ($key2 == 'maker_id') {
                    $where .= ' AND maker_id = ?';
                    $arrValues[] = $objFormParam->getValue($key);
                }
            }, $priority);
        }, $priority);
    }
}
