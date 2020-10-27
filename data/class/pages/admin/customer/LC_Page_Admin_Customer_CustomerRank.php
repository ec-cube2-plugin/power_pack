<?php

require_once POWERPACK_CLASS_REALDIR . 'pages/admin/plg_PowerPack_LC_Page_Admin.php';

/**
 * ランク管理 のページクラス.
 *
 * @package PowerPack
 */
class LC_Page_Admin_Customer_CustomerRank extends plg_PowerPack_LC_Page_Admin
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'customer/customer_rank.tpl';
        $this->tpl_mainno = 'customer';
        $this->tpl_subno = 'customer_rank';
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
        switch ($this->getMode()) {
            case 'delete':
                $this->lfDeleteData($_POST['id']);
                $this->setFlash('success', '削除しました。');
                SC_Response_Ex::sendRedirect('./customer_rank.php');
                break;
            default:
                $objQuery = SC_Query_Ex::getSingletonInstance();
                $this->arrCustomerRanks = $objQuery->select(
                    '*, (SELECT COUNT(*) FROM dtb_customer WHERE customer_rank_id = id AND del_flg = 0) as count',
                    'plg_PowerPack_dtb_customer_rank',
                    'del_flg = 0'
                );
                break;
        }
    }

    /**
     * 削除
     *
     * @param  integer $id ID
     * @return void
     */
    public function lfDeleteData($id)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();

        $count = $objQuery->count('dtb_customer', 'customer_rank_id = ?', $id);
        if ($count > 0) {
            throw new Exception();
        }

        $sqlval['del_flg'] = 1;
        $objQuery->update('plg_PowerPack_dtb_customer_rank', $sqlval, 'id = ?', array($id));
    }
}
