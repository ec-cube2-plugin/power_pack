<?php

/**
 * LC_Page_Admin_PowerPack_Config
 *
 * @package PowerPack
 */
class LC_Page_Admin_PowerPack_Config extends plg_PowerPack_LC_Page_Admin
{
    public $arrConfigPath = array();

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->tpl_mainpage = POWERPACK_TEMPLATE_ADMIN_REALDIR . 'config.tpl';
        $this->setTemplate($this->tpl_mainpage);

        $this->tpl_maintitle = '';
        $this->tpl_subtitle = 'PowerPack for EC-CUBE 設定';
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
        $arrPlugin = SC_Plugin_Util_Ex::getPluginByPluginCode('PowerPack');
        $arrPluginConfig = unserialize($arrPlugin['free_field1']);

        $form = new LC_Form_Admin_PowerPack_Config();
        $form->setData($arrPluginConfig);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $form->bind($_POST);

            if ($form->isValid()) {
                $this->lfRegistData($arrPlugin['plugin_id'], $form->getDbArray());
                $this->setFlash('success', '登録が完了しました。');

                SC_Response_Ex::sendRedirect('./load_plugin_config.php?plugin_id='.$arrPlugin['plugin_id']);
            }
        }

        $this->form = $form->createView();
    }

    public function lfRegistData($plugin_id, $arrForm)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->update('dtb_plugin', array(
            'free_field1' => serialize($arrForm),
        ), 'plugin_id = ?', array($plugin_id));

        // コンパイルファイルのクリア処理
        SC_Utils_Ex::clearCompliedTemplate();
        $this->changeMasterData($arrForm);
    }

    public function changeMasterData($arrForm)
    {
        $masterData = new SC_DB_MasterData_Ex();
        $masterData->objQuery = SC_Query_Ex::getSingletonInstance();
        $masterData->objQuery->begin();

        PowerPack::hook('LC_Page_Admin_PowerPack_Config::changeMasterData', array($this, $arrForm));

        $masterData->objQuery->commit();
    }

}
