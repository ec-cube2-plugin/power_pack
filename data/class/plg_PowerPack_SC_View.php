<?php

require_once CLASS_REALDIR . 'SC_View.php';

class plg_PowerPack_SC_View extends SC_View
{

    public function init()
    {
        parent::init();

        PowerPack::hook('SC_View::init', array($this));
    }

    // テンプレートの処理結果を取得
    public function fetch($template)
    {
        PowerPack::hook('SC_View::fetch', array($this, $template));

        return parent::fetch($template);
    }

}
