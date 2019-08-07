<?php

/**
 * plg_PowerPack_LC_Page_Admin_Products
 *
 * @package PowerPack
 */
class plg_PowerPack_LC_Page_Admin_Products extends LC_Page_Admin_Products
{

    /**
     * init
     */
    public function init()
    {
        parent::init();

        PowerPack::hook('LC_Page_Admin_Products::init', array($this));
    }

    /**
     * クエリを構築する.
     *
     * 検索条件のキーに応じた WHERE 句と, クエリパラメーターを構築する.
     * クエリパラメーターは, SC_FormParam の入力値から取得する.
     *
     * 構築内容は, 引数の $where 及び $arrValues にそれぞれ追加される.
     *
     * @param  string       $key          検索条件のキー
     * @param  string       $where        構築する WHERE 句
     * @param  array        $arrValues    構築するクエリパラメーター
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param  SC_FormParam $objDb        SC_Helper_DB_Ex インスタンス
     * @return void
     */
    public function buildQuery($key, &$where, &$arrValues, $objFormParam, $objDb)
    {
        parent::buildQuery($key, $where, $arrValues, $objFormParam, $objDb);

        PowerPack::hook('LC_Page_Admin_Products::buildQuery', array($this, $key, &$where, &$arrValues, $objFormParam, $objDb));
    }

    /**
     * 商品を検索する.
     *
     * @param  string     $where      検索条件の WHERE 句
     * @param  array      $arrValues  検索条件のパラメーター
     * @param  integer    $limit      表示件数
     * @param  integer    $offset     開始件数
     * @param  string     $order      検索結果の並び順
     * @param  SC_Product $objProduct SC_Product インスタンス
     * @return array      商品の検索結果
     */
    public function findProducts($where, $arrValues, $limit, $offset, $order, $objProduct)
    {
        PowerPack::hook('LC_Page_Admin_Products::findProducts', array($this, &$where, &$arrValues, &$limit, &$offset, &$order, $objProduct));

        return parent::findProducts($where, $arrValues, $limit, $offset, $order, $objProduct);
    }

}
