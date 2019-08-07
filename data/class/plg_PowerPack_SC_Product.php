<?php

class plg_PowerPack_SC_Product extends SC_Product
{

    /**
     * SC_Query インスタンスに設定された検索条件を使用して商品規格を取得する.
     *
     * @param  SC_Query $objQuery SC_Queryインスタンス
     * @param  array    $params   検索パラメーターの配列
     * @return array    商品規格の配列
     */
    public function getProductsClassByQuery(&$objQuery, $params)
    {
        // 末端の規格を取得
        $col = <<< __EOS__
            T1.product_id,
            T1.stock,
            T1.stock_unlimited,
            T1.sale_limit,
            T1.price01,
            T1.price02,
            T1.point_rate,
            T1.product_code,
            T1.product_class_id,
            T1.del_flg,
            T1.product_type_id,
            T1.down_filename,
            T1.down_realfilename,
            T3.name AS classcategory_name1,
            T3.rank AS rank1,
            T4.name AS class_name1,
            T4.class_id AS class_id1,
            T1.classcategory_id1,
            T1.classcategory_id2,
            dtb_classcategory2.name AS classcategory_name2,
            dtb_classcategory2.rank AS rank2,
            dtb_class2.name AS class_name2,
            dtb_class2.class_id AS class_id2
__EOS__;
        $table = <<< __EOS__
            dtb_products_class T1
            LEFT JOIN dtb_classcategory T3
                ON T1.classcategory_id1 = T3.classcategory_id
            LEFT JOIN dtb_class T4
                ON T3.class_id = T4.class_id
            LEFT JOIN dtb_classcategory dtb_classcategory2
                ON T1.classcategory_id2 = dtb_classcategory2.classcategory_id
            LEFT JOIN dtb_class dtb_class2
                ON dtb_classcategory2.class_id = dtb_class2.class_id
__EOS__;

        $objQuery->andWhere(' T3.classcategory_id is not null AND dtb_classcategory2.classcategory_id is not null ');
        $objQuery->setOrder('T3.rank DESC, dtb_classcategory2.rank DESC'); // XXX

        PowerPack::hook('SC_Product::getProductsClassByQuery', array($this, &$objQuery, &$params, &$col, &$table));

        $arrRet = $objQuery->select($col, $table, '', $params);

        return $arrRet;
    }

    /**
     * 商品規格IDから商品規格を取得する.
     *
     * 削除された商品規格は取得しない.
     *
     * @param  integer $productClassId 商品規格ID
     * @return array   商品規格の配列
     */
    public function getProductsClass($productClassId)
    {
        $arrProduct = parent::getProductsClass($productClassId);

        PowerPack::hook('SC_Product::getProductsClass', array($this, &$productClassId, &$arrProduct));

        return $arrProduct;
    }

    /**
     * SC_Queryインスタンスに設定された検索条件を元に並び替え済みの検索結果商品IDの配列を取得する。
     *
     * 検索条件は, SC_Query::setWhere() 関数で設定しておく必要があります.
     *
     * @param  SC_Query $objQuery SC_Query インスタンス
     * @param  array    $arrVal   検索パラメーターの配列
     * @return array    商品IDの配列
     */
    public function findProductIdsOrder(&$objQuery, $arrVal = array())
    {
        PowerPack::hook('SC_Product::findProductIdsOrder', array($this, &$objQuery, &$arrVal));

        return parent::findProductIdsOrder($objQuery, $arrVal);
    }

    /**
     * SC_Queryインスタンスに設定された検索条件をもとに対象商品数を取得する.
     *
     * 検索条件は, SC_Query::setWhere() 関数で設定しておく必要があります.
     *
     * @param  SC_Query $objQuery SC_Query インスタンス
     * @param  array    $arrVal   検索パラメーターの配列
     * @return integer    対象商品ID数
     */
    public function findProductCount(&$objQuery, $arrVal = array())
    {
        PowerPack::hook('SC_Product::findProductCount', array($this, &$objQuery, &$arrVal));

        return parent::findProductCount($objQuery, $arrVal);
    }

    /**
     * 商品詳細を取得する.
     *
     * @param  integer $product_id 商品ID
     * @return array   商品詳細情報の配列
     */
    public function getDetail($product_id)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();

        $col = '*';
        $from = $this->alldtlSQL();
        $where = 'product_id = ?';
        $arrWhereVal = array($product_id);

        PowerPack::hook('SC_Product::getDetail', array($this, &$product_id, &$col, &$from, &$where, &$arrWhereVal));

        $arrProduct = (array)$objQuery->getRow($col, $from, $where, $arrWhereVal);

        // 税込金額を設定する
        SC_Product_Ex::setIncTaxToProduct($arrProduct);

        return $arrProduct;
    }

    /**
     * SC_Queryインスタンスに設定された検索条件をもとに商品一覧の配列を取得する.
     *
     * 主に SC_Product::findProductIds() で取得した商品IDを検索条件にし,
     * SC_Query::setOrder() や SC_Query::setLimitOffset() を設定して, 商品一覧
     * の配列を取得する.
     *
     * @param  SC_Query $objQuery SC_Query インスタンス
     * @return array    商品一覧の配列
     */
    public function lists(&$objQuery)
    {
        $col = <<< __EOS__
             product_id
            ,product_code_min
            ,product_code_max
            ,name
            ,comment1
            ,comment2
            ,comment3
            ,main_list_comment
            ,main_image
            ,main_list_image
            ,price01_min
            ,price01_max
            ,price02_min
            ,price02_max
            ,stock_min
            ,stock_max
            ,stock_unlimited_min
            ,stock_unlimited_max
            ,deliv_date_id
            ,status
            ,del_flg
            ,update_date
__EOS__;
        $from = $this->alldtlSQL();

        PowerPack::hook('SC_Product::lists', array($this, $objQuery, &$col, &$from));

        $res = $objQuery->select($col, $from);

        return $res;
    }

    /**
     * 商品情報の配列に, 税込金額を設定して返す.
     *
     * この関数は, 主にスマートフォンで使用します.
     *
     * @param  array $arrProducts 商品情報の配列
     * @return array 旧バージョン互換用のデータ
     */
    public static function setPriceTaxTo(&$arrProducts)
    {
        parent::setPriceTaxTo($arrProducts);

        PowerPack::hook('SC_Product::setPriceTaxTo', array(&$arrProducts));

        return $arrProducts;
    }

    /**
     * 商品情報の配列に税込金額を設定する
     *
     * @param  array $arrProduct 商品情報の配列
     * @return void
     */
    public static function setIncTaxToProduct(&$arrProduct)
    {
        parent::setIncTaxToProduct($arrProduct);

        PowerPack::hook('SC_Product::setIncTaxToProduct', array(&$arrProduct));
    }
}
