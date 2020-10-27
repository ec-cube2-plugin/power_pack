<?php

/**
 * ランク編集 のページクラス.
 *
 * @package PowerPack
 */
class LC_Page_Admin_Customer_CustomerRankEdit extends plg_PowerPack_LC_Page_Admin
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'customer/customer_rank_edit.tpl';
        $this->tpl_mainno = 'customer';
        $this->tpl_subno = 'customer_rank_edit';

        $masterData = new SC_DB_MasterData_Ex();
        $this->tpl_maintitle = '顧客管理';
        $this->tpl_subtitle = 'ランク管理';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        $form = new LC_Form_Admin_Customer_CustomerRank();

        if ($_REQUEST['id']) {
            $arrCustomerRank = $this->lfGetData($_REQUEST['id']);
            if (!$arrCustomerRank) {
                throw new NotFoundException();
            }
            $form->setData($arrCustomerRank);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $form->bind($_POST);

            if ($form->isValid()) {
                $this->lfRegistData($form);
                $this->setFlash('success', '登録が完了しました。');
                SC_Response_Ex::sendRedirect('./customer_rank_edit.php?id='.$form['id']);
            }
        }

        $this->form = $form->createView();
    }

    /**
     * 情報のDB取得
     *
     * @param  integer $id ID
     * @return array   data
     */
    public function lfGetData($id)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        return $objQuery->getRow('*', 'plg_PowerPack_dtb_customer_rank', 'del_flg = 0 AND id = ?', array($id));
    }

    /**
     * 情報の更新
     *
     * @param  SC_FormParam $form SC_FormParam インスタンス
     * @return void
     */
    public function lfRegistData($form)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $arrValues = $form->getDbArray();
        $arrValues['update_date'] = 'CURRENT_TIMESTAMP';

        if ($form['id']) {
            $objQuery->update('plg_PowerPack_dtb_customer_rank', $arrValues, 'id = ?', array($form['id']));
        } else {
            $arrValues['id'] = $objQuery->nextVal('plg_PowerPack_dtb_customer_rank_id');
            $arrValues['create_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->insert('plg_PowerPack_dtb_customer_rank', $arrValues);

            $form['id'] = $arrValues['id'];
        }
    }
}
