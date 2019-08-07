<?php

require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * メールアドレスチェック のページクラス.
 *
 * @package PowerPack
 */
class LC_Page_CheckEmail extends LC_Page_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        $this->skip_load_page_layout = true;
        parent::init();
        $this->tpl_message = '住所を検索しています。';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $arrErr = $this->fnErrorCheck($_POST);
        if (count($arrErr) > 0) {
            echo 'ERROR';
        } else {
            $CONF = SC_Helper_DB_Ex::sfGetBasisData();
            $CheckUser = new Mail_CheckUser($_SERVER['SERVER_NAME'], $CONF['email03']);
            if ($CheckUser->checkEmail($_POST['email'])) {
                echo 'OK';
            } else {
                echo 'WARNING';
            }
        }
    }

    /**
     * 入力エラーのチェック.
     *
     * @param  array $arrRequest リクエスト値
     * @return array $arrErr エラーメッセージ配列
     */
    public function fnErrorCheck($arrRequest)
    {
        $objFormParam = new SC_FormParam_Ex();
        $objFormParam->addParam('メールアドレス', 'email', null, 'a', array('EXIST_CHECK', 'NO_SPTAB', 'EMAIL_CHECK', 'SPTAB_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->setParam($arrRequest);

        return $objFormParam->checkError();
    }
}
