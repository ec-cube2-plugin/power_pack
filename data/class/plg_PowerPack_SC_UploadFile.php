<?php

/* アップロードファイル管理クラス */
class plg_PowerPack_SC_UploadFile extends SC_UploadFile
{
    public static $arrFile = array();
    public $class = '';

    // ファイル管理クラス
    public function __construct($temp_dir, $save_dir)
    {
        parent::__construct($temp_dir, $save_dir);

        // SC_UploadFile のフックポイント
        $backtraces = debug_backtrace();
        // 呼び出し元のクラスを取得
        $this->class = $backtraces[1]['class'];
        $objPage = $backtraces[1]['object'];
        $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($objPage->plugin_activate_flg);
        if (is_object($objPlugin)) {
            $objPlugin->doAction('SC_UploadFile_construct', array($this->class, $this));
        }
    }

    public function getDBFileList()
    {
        $dbFileList = parent::getDBFileList();

        PowerPack::hook('SC_UploadFile::getDBFileList', array($this, &$dbFileList));

        return $dbFileList;
    }
}
