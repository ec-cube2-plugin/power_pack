<?php

require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * LC_Page_News のページクラス.
 *
 * @package PowerPack
 */
class LC_Page_News_Detail extends LC_Page_Ex
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

        $this->arrNews = $objNews->getNews($_GET['news_id']);
        $this->tpl_title = '新着情報';
        $this->tpl_subtitle = $this->arrNews['news_title'];
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
