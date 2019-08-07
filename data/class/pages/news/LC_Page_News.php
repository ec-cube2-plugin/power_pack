<?php

require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * LC_Page_News のページクラス.
 *
 * @package PowerPack
 */
class LC_Page_News extends LC_Page_Ex
{

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init()
    {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process()
    {
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action()
    {
        $objNews = new SC_Helper_News_Ex();

        $this->objNavi = new SC_PageNavi_Ex(
            $_REQUEST['pageno'],
            $objNews->getCount(),
            SEARCH_PMAX,
            'eccube.movePage',
            NAVI_PMAX,
            'pageno=#page#',
            SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE
        );

        $this->arrNewses = $objNews->getList(SEARCH_PMAX, $_REQUEST['pageno']);
        $this->tpl_title = '新着情報';
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy()
    {
        parent::destroy();
    }

}
