<?php

/**
 * 会員情報の登録・編集・検索ヘルパークラス.
 *
 * @package PowerPack
 */
class plg_PowerPack_SC_Helper_Customer extends SC_Helper_Customer
{

    /**
     * 会員・顧客・お届け先共通
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param string       $prefix       キー名にprefixを付ける場合に指定
     * @access public
     * @return void
     */
    public function sfCustomerCommonParam(&$objFormParam, $prefix = '')
    {
        parent::sfCustomerCommonParam($objFormParam, $prefix);

        PowerPack::hook('SC_Helper_Customer::sfCustomerCommonParam', array($objFormParam, $prefix));
    }

    /**
     * 会員登録共通
     *
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param  boolean      $isAdmin      true:管理者画面 false:会員向け
     * @param  boolean      $is_mypage    マイページの場合 true
     * @param  string       $prefix       キー名にprefixを付ける場合に指定
     * @return void
     */
    public function sfCustomerRegisterParam(&$objFormParam, $isAdmin = false, $is_mypage = false, $prefix = '')
    {
        parent::sfCustomerRegisterParam($objFormParam, $isAdmin, $is_mypage, $prefix);

        PowerPack::hook('SC_Helper_Customer::sfCustomerRegisterParam', array($objFormParam, $isAdmin, $is_mypage, $prefix));
    }

    /**
     * 会員検索パラメーター（管理画面用）
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @access public
     * @return void
     */
    public function sfSetSearchParam(&$objFormParam)
    {
        parent::sfSetSearchParam($objFormParam);

        PowerPack::hook('SC_Helper_Customer::sfSetSearchParam', array($objFormParam));
    }

    /**
     * 会員情報変更エラーチェック
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param boolean      $isAdmin      管理画面チェック時:true
     * @access public
     * @return array エラーの配列
     */
    public function sfCustomerMypageErrorCheck(&$objFormParam, $isAdmin = false)
    {
        $arrErr = parent::sfCustomerMypageErrorCheck($objFormParam, $isAdmin);

        PowerPack::hook('SC_Helper_Customer::sfCustomerMypageErrorCheck', array($objFormParam, $isAdmin, $arrErr));

        return $arrErr;
    }

}
