<?php

/**
 * Class SC_PluginProvider_Admin_ProductAction
 *
 * 管理画面＞商品管理 で一括作業を行えるようにする
 */
class SC_PluginProvider_Admin_ProductAction extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        PowerPack::addPrefilter(DEVICE_TYPE_ADMIN, 'products/index.tpl', function(&$source) {
            $objTransform = new SC_Helper_Transform($source);
            $objTransform->select('#products-search-result', 0)->insertBefore('<!--{include file=\'products/_index_action.tpl\'}-->');
            $objTransform->select('#products-search-result col', 4)->replaceElement('<col width="20%" />');
            $objTransform->select('#products-search-result col', 0)->insertBefore('<col width="5%" />');
            $objTransform->select('#products-search-result tr', 0)->appendFirst('<th rowspan="2"><input type="checkbox" id="action_product_id_all" /></th>');
            $objTransform->select('#products-search-result tr', 2)->appendFirst('<td rowspan="2" class="menu"><input type="checkbox" class="action_product_id" name="action_product_id[]" value="<!--{$arrProducts[cnt].product_id}-->" /></td>');
            $source = $objTransform->getHTML();
        });
        PowerPack::addAction('LC_Page_Admin_Products::init', function (plg_PowerPack_LC_Page_Admin_Products $objPage) {
            $objPage->arrAction = array(
                'category_add' => 'カテゴリ 追加',
                'category_del' => 'カテゴリ 削除',
                'status_edit' => '種別 変更',
                'maker_edit' => 'メーカー 変更',
                'product_statuses_add' => '商品ステータス 追加',
                'product_statuses_del' => '商品ステータス 削除',
            );

            $objPage->arrMaker = SC_Helper_DB_Ex::sfGetIDValueList("dtb_maker", "maker_id", 'name', 'del_flg = 0');

            if ($objPage->getMode() == 'action') {
                $_REQUEST['mode'] = 'search';
                $_POST['mode'] = 'search';

                $objFormParam = new SC_FormParam_Ex();
                $objQuery = SC_Query_Ex::getSingletonInstance();

                // パラメーター情報の初期化
                $objPage->lfInitParam($objFormParam);
                $objFormParam->addParam('商品ID', 'action_product_id');
                $objFormParam->addParam('アクション', 'action', STEXT_LEN, 'n', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('カテゴリ', 'action_category', STEXT_LEN, 'n', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('種別', 'action_status', INT_LEN, 'n', array('MAX_LENGTH_CHECK'));
                $objFormParam->addParam('メーカー', 'action_maker', INT_LEN, 'n', array('MAX_LENGTH_CHECK'));
                $objFormParam->addParam('商品ステータス', 'action_product_statuses', INT_LEN, 'n', array('MAX_LENGTH_CHECK'));
                $objFormParam->setParam($_POST);

                $objFormParam->convParam();
                $objFormParam->trimParam();
                $arrParam = $objFormParam->getHashArray();

                $objErr = new SC_CheckError_Ex($objFormParam->getHashArray());
                $objErr->arrErr = $objFormParam->checkError();
                $objPage->arrErr = $objErr->doFunc(array('開始日', '終了日', 'search_startyear', 'search_startmonth', 'search_startday', 'search_endyear', 'search_endmonth', 'search_endday'), array('CHECK_SET_TERM'));
                $objPage->arrErr = $objErr->doFunc(array('アクション', 'action'), array('EXIST_CHECK'));

                switch ($arrParam['action']) {
                    case "category_add":
                    case "category_del":
                        $objPage->arrErr = $objErr->doFunc(array('カテゴリ', 'action_category'), array('EXIST_CHECK'));
                        break;
                    case "status_edit":
                        $objPage->arrErr = $objErr->doFunc(array('種別', 'action_status'), array('EXIST_CHECK'));
                        break;
                    case "maker_edit":
                        $objPage->arrErr = $objErr->doFunc(array('メーカー', 'action_maker'), array('EXIST_CHECK'));
                        break;
                    case "product_statuses_add":
                    case "product_statuses_del":
                        $objPage->arrErr = $objErr->doFunc(array('商品ステータス', 'action_product_statuses'), array('EXIST_CHECK'));
                        break;
                    default:
                        PowerPack::hook('PowerPack::LC_Page_Admin_Products::checkError', array($arrParam['action'], &$objPage->arrErr, $objErr), array());
                        break;
                }

                if (count($objPage->arrErr) == 0) {
                    $productIds = $arrParam['action_product_id'];
                    foreach ($productIds as $product_id) {
                        switch ($arrParam['action']) {
                            case "category_add":
                                $table = 'dtb_product_categories';
                                $where = "product_id = ? AND category_id = ?";
                                $exists = $objQuery->exists($table, $where, array($product_id, $arrParam['action_category']));

                                if (!$exists) {
                                    $sqlval['product_id'] = $product_id;
                                    $sqlval['category_id'] = $arrParam['action_category'];
                                    $sqlval['rank'] = 1;
                                    $objQuery->insert($table, $sqlval);
                                }
                                break;
                            case "category_del":
                                $table = 'dtb_product_categories';
                                $where = "product_id = ? AND category_id = ?";
                                $objQuery->delete($table, $where, array($product_id, $arrParam['action_category']));
                                break;
                            case "status_edit":
                                $table = 'dtb_products';
                                $where = "product_id = ?";
                                $rankCount = $objQuery->update($table, array(
                                    'status' => $arrParam['action_status'],
                                ), $where, array($product_id));
                                break;
                            case "maker_edit":
                                $table = 'dtb_products';
                                $where = "product_id = ?";
                                $rankCount = $objQuery->update($table, array(
                                    'maker_id' => $arrParam['action_maker'],
                                ), $where, array($product_id));
                                break;
                            case "product_statuses_add":
                                $table = 'dtb_product_status';
                                $where = "product_id = ? AND product_status_id = ?";
                                $exists = $objQuery->exists($table, $where, array($product_id, $arrParam['action_product_statuses']));

                                if (!$exists) {
                                    $sqlval['product_id'] = $product_id;
                                    $sqlval['product_status_id'] = $arrParam['action_product_statuses'];
                                    $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
                                    $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
                                    $sqlval['creator_id'] = $_SESSION['member_id'];
                                    $objQuery->insert($table, $sqlval);
                                }
                                break;
                            case "product_statuses_del":
                                $table = 'dtb_product_status';
                                $where = "product_id = ? AND product_status_id = ?";
                                $objQuery->delete($table, $where, array($product_id, $arrParam['action_product_statuses']));
                                break;
                            default:
                                PowerPack::hook('PowerPack::LC_Page_Admin_Products::action', array($arrParam['action']));
                                break;
                        }
                    }
                }
            }
        });
    }
}
