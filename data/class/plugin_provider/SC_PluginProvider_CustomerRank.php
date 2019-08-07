<?php

/**
 * Class SC_PluginProvider_CustomerRank
 *
 * 顧客ランク対応
 */
class SC_PluginProvider_CustomerRank extends SC_PluginProvider_Base
{
    public function install($arrPlugin, SC_Plugin_Installer $objPluginInstaller = null)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        // dtb_customer
        $objQuery->query(<<<__EOF__
            ALTER TABLE dtb_customer ADD customer_rank_id INT NOT NULL DEFAULT 1;
__EOF__
        );

        // plg_PowerPack_dtb_products_class_price
        $objQuery->query(<<<__EOF__
            CREATE TABLE plg_PowerPack_dtb_products_class_customer_rank (
                product_class_id INTEGER,
                customer_rank_id INTEGER,
                price DECIMAL(10) NOT NULL,
                create_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                update_date timestamp NOT NULL,
                PRIMARY KEY (product_class_id, customer_rank_id)
            );
__EOF__
        );

        // plg_PowerPack_dtb_customer_rank
        $objQuery->query(<<<__EOF__
            CREATE TABLE plg_PowerPack_dtb_customer_rank (
                id INTEGER,
                name TEXT,
                del_flg SMALLINT NOT NULL DEFAULT 0,
                create_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                update_date timestamp NOT NULL,
                PRIMARY KEY (id)
            );
__EOF__
        );
    }

    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        $provider = $this;

        // ランクのために毎回更新
        $objHelperPlugin->addAction('LC_Page_preProcess', function (LC_Page $objPage) {
            if (!$objPage instanceof LC_Page_FrontParts_Bloc && !$objPage instanceof LC_Page_Admin) {
                $objCustomer = new SC_Customer_Ex();
                $objCustomer->updateSession();
            }
        }, $priority);

        // 価格
        PowerPack::addAction('SC_Product::getProductsClass', function (SC_Product_Ex $objProduct, &$productClassId, &$arrProduct) {
            if (!SC_Utils_Ex::isBlank($arrProduct['price03'])) {
                $arrProduct['price03_inctax'] = SC_Helper_TaxRule_Ex::sfCalcIncTax($arrProduct['price03'], $arrProduct['product_id'], $productClassId);
            }
        });
        PowerPack::addAction('SC_Product::getDetail', function(SC_Product_Ex $objProduct, &$product_id, &$col, &$from, &$where, &$arrWhereVal) use ($provider) {
            if (DB_TYPE == 'mysql') {
                $objCustomer = new SC_Customer_Ex();
                $arrWhereVal = array($objCustomer->getValue('customer_rank_id'), $product_id);
                $from = $provider->alldtlSQL_MYSQL();
            }
        });
        PowerPack::addAction('SC_Product::lists', function(SC_Product_Ex $objProduct, $objQuery, &$col, &$from) use ($provider) {
            if (DB_TYPE == 'mysql') {
                $col = '*';
                $objCustomer = new SC_Customer_Ex();
                $objQuery->arrWhereVal = array_merge((array)$objCustomer->getValue('customer_rank_id'), (array)$objQuery->arrWhereVal);
                $from = $provider->alldtlSQL_MYSQL();
            }
        });
        PowerPack::addAction('SC_Product::setPriceTaxTo', function (&$arrProducts) {
            foreach ($arrProducts as &$arrProduct) {
                $arrProduct['price03_min_format'] = number_format($arrProduct['price03_min']);
                $arrProduct['price03_max_format'] = number_format($arrProduct['price03_max']);

                $arrProduct['price03_min_inctax_format'] = number_format($arrProduct['price03_min_inctax']);
                $arrProduct['price03_max_inctax_format'] = number_format($arrProduct['price03_max_inctax']);

                $arrProduct['price03_min_tax_format'] =& $arrProduct['price03_min_inctax_format'];
                $arrProduct['price03_max_tax_format'] =& $arrProduct['price03_max_inctax_format'];
            }
        });
        PowerPack::addAction('SC_Product::setIncTaxToProduct', function (&$arrProduct) {
            $arrProduct['price03_min_inctax'] = isset($arrProduct['price03_min']) ? SC_Helper_TaxRule_Ex::sfCalcIncTax($arrProduct['price03_min'], $arrProduct['product_id']) : null;
            $arrProduct['price03_max_inctax'] = isset($arrProduct['price03_max']) ? SC_Helper_TaxRule_Ex::sfCalcIncTax($arrProduct['price03_max'], $arrProduct['product_id']) : null;
        });
        PowerPack::addAction('SC_Product::getProductsClassByQuery', function(SC_Product_Ex $objProduct, SC_Query_Ex $objQuery, &$params, &$col, &$table) {
            // 末端の規格を取得
            $col = <<< __EOS__
            T1.*,
            CASE WHEN PC.price IS NOT NULL THEN PC.price ELSE T1.price02 END AS price03,
            T3.name AS classcategory_name1,
            T3.rank AS rank1,
            T4.name AS class_name1,
            T4.class_id AS class_id1,
            dtb_classcategory2.name AS classcategory_name2,
            dtb_classcategory2.rank AS rank2,
            dtb_class2.name AS class_name2,
            dtb_class2.class_id AS class_id2
__EOS__;
            $table = <<< __EOS__
            dtb_products_class T1
            LEFT JOIN dtb_classcategory T3
                ON T1.classcategory_id1 = T3.classcategory_id
            LEFT JOIN plg_PowerPack_dtb_products_class_customer_rank PC
                ON T1.product_class_id = PC.product_class_id AND PC.customer_rank_id = ?
            LEFT JOIN dtb_class T4
                ON T3.class_id = T4.class_id
            LEFT JOIN dtb_classcategory dtb_classcategory2
                ON T1.classcategory_id2 = dtb_classcategory2.classcategory_id
            LEFT JOIN dtb_class dtb_class2
                ON dtb_classcategory2.class_id = dtb_class2.class_id
__EOS__;

            $objCustomer = new SC_Customer_Ex();
            $params = array_merge((array) $objCustomer->getValue('customer_rank_id'), (array) $params);

            $arrWhere = explode(' ', $objQuery->where);
            $arrWhere = preg_replace('/\Aproduct_class_id\z/', 'T1.product_class_id', $arrWhere);
            $objQuery->where = implode(' ', $arrWhere);

            $objQuery->setOrder('T3.rank DESC, dtb_classcategory2.rank DESC');
        });


        //
        PowerPack::addPrefilter(DEVICE_TYPE_ADMIN, 'customer/subnavi.tpl', function(&$source) {
            $objTransform = new SC_Helper_Transform($source);
            $objTransform->select('ul', 0, false)->appendChild('<!--{include file=\'customer/_subnavi_customer_rank.tpl\'}-->');
            $source = $objTransform->getHTML();
        });
        PowerPack::addPrefilter(DEVICE_TYPE_ADMIN, 'customer/edit.tpl', function(&$source) {
            $objTransform = new SC_Helper_Transform($source);
            $objTransform->select('#customer table.form', 0)->appendChild('<!--{include file=\'customer/_edit_customer_rank.tpl\'}-->');
            $source = $objTransform->getHTML();
        });
        PowerPack::addPrefilter(DEVICE_TYPE_ADMIN, 'customer/edit_confirm.tpl', function(&$source) {
            $objTransform = new SC_Helper_Transform($source);
            $objTransform->select('#customer table.form', 0)->appendChild('<!--{include file=\'customer/_edit_confirm_customer_rank.tpl\'}-->');
            $source = $objTransform->getHTML();
        });
        PowerPack::addPrefilter(DEVICE_TYPE_ADMIN, 'adminparts/form_customer_search.tpl', function(&$source) {
            $source .= '<!--{include file=\'adminparts/_form_customer_search_customer_rank.tpl\'}-->';
        });
        PowerPack::addPrefilter(DEVICE_TYPE_ADMIN, 'mail/query.tpl', function(&$source) {
            $objTransform = new SC_Helper_Transform($source);
            $objTransform->select('#mail table.form tr', 17)->insertAfter('<!--{include file=\'mail/_query_customer_rank.tpl\'}-->');
            $source = $objTransform->getHTML();
        });
        PowerPack::addAction('SC_Helper_Customer::sfSetSearchParam', function ($objFormParam) {
            $objFormParam->addParam('顧客ランク', 'search_customer_rank_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK'));
        });
        PowerPack::addFormParam('LC_Page_Admin_Customer_Edit', function (plg_PowerPack_SC_FormParam $objFormParam) use ($priority) {
            $objFormParam->addParam('顧客ランク', 'customer_rank_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK'));
        }, $priority);
        $objHelperPlugin->addAction("LC_Page_Admin_Customer_action_before", function (LC_Page_Admin_Customer $objPage) {
            $objQuery = SC_Query_Ex::getSingletonInstance();
            $objQuery->setOrder('id ASC');
            $arrRanks = $objQuery->select('*', 'plg_PowerPack_dtb_customer_rank', 'del_flg = 0');
            $objPage->arrRanks = array();
            foreach ($arrRanks as $arrRank) {
                $objPage->arrRanks[$arrRank['id']] = $arrRank['name'];
            }
        }, $priority);
        $objHelperPlugin->addAction("LC_Page_Admin_Customer_SearchCustomer_action_before", function (LC_Page_Admin_Customer_SearchCustomer $objPage) {
            $objQuery = SC_Query_Ex::getSingletonInstance();
            $objQuery->setOrder('id ASC');
            $arrRanks = $objQuery->select('*', 'plg_PowerPack_dtb_customer_rank', 'del_flg = 0');
            $objPage->arrRanks = array();
            foreach ($arrRanks as $arrRank) {
                $objPage->arrRanks[$arrRank['id']] = $arrRank['name'];
            }
        }, $priority);
        $objHelperPlugin->addAction("LC_Page_Admin_Mail_action_before", function (LC_Page_Admin_Mail $objPage) {
            $objQuery = SC_Query_Ex::getSingletonInstance();
            $objQuery->setOrder('id ASC');
            $arrRanks = $objQuery->select('*', 'plg_PowerPack_dtb_customer_rank', 'del_flg = 0');
            $objPage->arrRanks = array();
            foreach ($arrRanks as $arrRank) {
                $objPage->arrRanks[$arrRank['id']] = $arrRank['name'];
            }
        }, $priority);
        PowerPack::addAction('SC_CustomerList::__construct', function (plg_PowerPack_SC_CustomerList $objSelect, $array, $mode) {
            if (!isset($objSelect->arrSql['search_customer_rank_id'])) $objSelect->arrSql['search_customer_rank_id'] = '';
            if (is_array($objSelect->arrSql['search_customer_rank_id'])) {
                $arrVal = $objSelect->setItemTerm($objSelect->arrSql['search_customer_rank_id'], 'customer_rank_id');
                foreach ($arrVal as $data) {
                    $objSelect->arrVal[] = $data;
                }
            }
        });


        // LC_Page_Mypage_History
        $objHelperPlugin->addAction('LC_Page_Mypage_History_action_after', function (LC_Page_Mypage_History $objPage) {
            $objProduct = new SC_Product_Ex();
            $objPage->is_price_change  = false;
            foreach ($objPage->tpl_arrOrderDetail as $product_index => $arrOrderProductDetail) {
                //必要なのは商品の販売金額のみなので、遅い場合は、別途SQL作成した方が良い
                $arrTempProductDetail = $objProduct->getProductsClass($arrOrderProductDetail['product_class_id']);

                $arrTempProductDetail['price03_inctax'] = SC_Helper_TaxRule_Ex::sfCalcIncTax(
                    $arrTempProductDetail['price03'],
                    $arrTempProductDetail['product_id'],
                    $arrTempProductDetail['product_class_id']
                );
                if ($objPage->tpl_arrOrderDetail[$product_index]['price_inctax'] != $arrTempProductDetail['price03_inctax']) {
                    $objPage->is_price_change = true;
                }
                $objPage->tpl_arrOrderDetail[$product_index]['product_price_inctax'] = ($arrTempProductDetail['price03_inctax']) ? $arrTempProductDetail['price03_inctax'] : 0 ;
            }
        }, $priority);


        // LC_Page_Admin_Products_Product
        PowerPack::addPrefilter(DEVICE_TYPE_ADMIN, 'products/product.tpl', function(&$source) {
            $objTransform = new SC_Helper_Transform($source);
            $objTransform->select('#products table.form tr', 11)->insertAfter('<!--{include file=\'products/_product_price.tpl\'}-->');
            $source = $objTransform->getHTML();
        });
        PowerPack::addPrefilter(DEVICE_TYPE_ADMIN, 'products/confirm.tpl', function(&$source) {
            $objTransform = new SC_Helper_Transform($source);
            $objTransform->select('#products table tr', 9)->insertAfter('<!--{include file=\'products/_confirm_price.tpl\'}-->');
            $source = $objTransform->getHTML();
        });
        PowerPack::addFormParam('LC_Page_Admin_Products_Product', function (plg_PowerPack_SC_FormParam $objFormParam) use ($priority) {
            $objQuery = SC_Query_Ex::getSingletonInstance();
            $objQuery->setOrder('id ASC');
            $arrRanks = $objQuery->select('*', 'plg_PowerPack_dtb_customer_rank', 'del_flg = 0');

            if ($_REQUEST['mode'] != 'pre_edit' && $_REQUEST['mode'] != 'copy') {
                // ランク価格
                foreach ($arrRanks as $arrRank) {
                    $objFormParam->addParam($arrRank['name'] . '価格', 'price_rank_' . $arrRank['id'], PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK', 'ZERO_START'));
                }
            }

            PowerPack::addAction('SC_UploadFile::getDBFileList', function (plg_PowerPack_SC_UploadFile $objUploadFile, &$dbFileList) use ($objFormParam, $priority, $arrRanks) {
                $arrForm = $objFormParam->getHashArray();
                // ランク価格
                PowerPack::addAction('LC_Page_Admin_Products_Product::action_after', function (LC_Page_Admin_Products_Product $objPage) use ($arrForm, $arrRanks, $priority) {
                    $product_id = $objPage->arrForm['product_id'];
                    $objDb = new SC_Helper_DB_Ex();
                    if ($objDb->sfHasProductClass($product_id) == false) {
                        $product_class_id = SC_Utils_Ex::sfGetProductClassId($product_id, '0', '0');
                    }

                    $objQuery = SC_Query_Ex::getSingletonInstance();
                    if ($product_class_id) {
                        $objQuery->delete('plg_PowerPack_dtb_products_class_customer_rank', 'product_class_id = ?', array($product_class_id));
                        foreach ($arrRanks as $arrRank) {
                            $key = 'price_rank_' . $arrRank['id'];
                            if ($arrForm[$key] !== '' && $arrForm[$key] !== null) {
                                $objQuery->insert('plg_PowerPack_dtb_products_class_customer_rank', array(
                                    'product_class_id' => $product_class_id,
                                    'customer_rank_id' => $arrRank['id'],
                                    'price' => $arrForm[$key],
                                ));
                            }
                        }
                    }
                }, $priority);
            }, $priority);
        }, $priority);

        $objHelperPlugin->addAction("LC_Page_Admin_Products_Product_action_before", array($this, "LC_Page_Admin_Products_Product_action_before"), $priority);
        $objHelperPlugin->addAction("LC_Page_Admin_Products_Product_action_after", array($this, "LC_Page_Admin_Products_Product_action_after"), $priority);
        $objHelperPlugin->addAction("LC_Page_Admin_Customer_Edit_action_after", array($this, "LC_Page_Admin_Customer_Edit_action_after"), $priority);
    }

    public function LC_Page_Admin_Products_Product_action_before(LC_Page_Admin_Products_Product $objPage)
    {
        // ランク価格
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->setOrder('id ASC');
        $objPage->arrRanks = $objQuery->select('*', 'plg_PowerPack_dtb_customer_rank', 'del_flg = 0');
    }

    public function LC_Page_Admin_Products_Product_action_after(LC_Page_Admin_Products_Product $objPage)
    {
        if ($objPage->getMode() == 'pre_edit' || $objPage->getMode() == 'copy') {
            // 価格挿入
            $objQuery = SC_Query_Ex::getSingletonInstance();
            $arrRankPrices = $objQuery->select('*', 'plg_PowerPack_dtb_products_class_customer_rank', 'product_class_id = ?', array($objPage->arrForm['product_class_id']));
            foreach ($arrRankPrices as $arrRankPrice) {
                $objPage->arrForm['price_rank_' . $arrRankPrice['customer_rank_id']] = $arrRankPrice['price'];
            }
        }
    }

    public function LC_Page_Admin_Customer_Edit_action_after(LC_Page_Admin_Customer_Edit $objPage)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->setOrder('id ASC');
        $arrRanks = $objQuery->select('*', 'plg_PowerPack_dtb_customer_rank', 'del_flg = 0');
        $objPage->arrRanks = array();
        foreach ($arrRanks as $arrRank) {
            $objPage->arrRanks[$arrRank['id']] = $arrRank['name'];
        }
    }

    /**
     * 商品詳細の SQL を取得する.
     *
     * @param  string $where_products_class 商品規格情報の WHERE 句
     * @return string 商品詳細の SQL
     */
    public function alldtlSQL_MYSQL($where_products_class = '')
    {
        if (!SC_Utils_Ex::isBlank($where_products_class)) {
            $where_products_class = 'AND (' . $where_products_class . ')';
        }
        /*
         * point_rate, deliv_fee は商品規格(dtb_products_class)ごとに保持しているが,
         * 商品(dtb_products)ごとの設定なので MAX のみを取得する.
         */
        $sql = <<< __EOS__
            (
                SELECT
                     dtb_products.*
                    ,T4.product_code_min
                    ,T4.product_code_max
                    ,T4.price01_min
                    ,T4.price01_max
                    ,T4.price02_min
                    ,T4.price02_max
                    ,T4.price03_min
                    ,T4.price03_max
                    ,T4.stock_min
                    ,T4.stock_max
                    ,T4.stock_unlimited_min
                    ,T4.stock_unlimited_max
                    ,T4.point_rate
                    ,T4.deliv_fee
                    ,dtb_maker.name AS maker_name
                FROM dtb_products
                    INNER JOIN (
                        SELECT product_id
                            ,MIN(product_code) AS product_code_min
                            ,MAX(product_code) AS product_code_max
                            ,MIN(price01) AS price01_min
                            ,MAX(price01) AS price01_max
                            ,MIN(price02) AS price02_min
                            ,MAX(price02) AS price02_max
                            ,MIN(CASE WHEN PC.price IS NOT NULL THEN PC.price ELSE price02 END) AS price03_min
                            ,MAX(CASE WHEN PC.price IS NOT NULL THEN PC.price ELSE price02 END) AS price03_max
                            ,MIN(stock) AS stock_min
                            ,MAX(stock) AS stock_max
                            ,MIN(stock_unlimited) AS stock_unlimited_min
                            ,MAX(stock_unlimited) AS stock_unlimited_max
                            ,MAX(point_rate) AS point_rate
                            ,MAX(deliv_fee) AS deliv_fee
                        FROM dtb_products_class
                        LEFT JOIN plg_PowerPack_dtb_products_class_customer_rank PC
                            ON dtb_products_class.product_class_id = PC.product_class_id AND PC.customer_rank_id = ?
                        WHERE del_flg = 0 $where_products_class
                        GROUP BY product_id
                    ) AS T4
                        ON dtb_products.product_id = T4.product_id
                    LEFT JOIN dtb_maker
                        ON dtb_products.maker_id = dtb_maker.maker_id
            ) AS alldtl
__EOS__;

        return $sql;
    }
}
