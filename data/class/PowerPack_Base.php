<?php

/**
 * プラグインのメインクラス
 *
 * @package PowerPack
 */
abstract class PowerPack_Base extends SC_Plugin_Base
{

    public static $arrPrefilters = array(
        DEVICE_TYPE_PC => array(),
        DEVICE_TYPE_SMARTPHONE => array(),
        DEVICE_TYPE_MOBILE => array(),
        DEVICE_TYPE_ADMIN => array(),
    );
    public static $arrOutputfilters = array(
        DEVICE_TYPE_PC => array(),
        DEVICE_TYPE_SMARTPHONE => array(),
        DEVICE_TYPE_MOBILE => array(),
        DEVICE_TYPE_ADMIN => array(),
    );
    public static $arrLoadClassFileChange = array();
    public static $arrTemplateDirs = array(
        DEVICE_TYPE_PC => array(),
        DEVICE_TYPE_SMARTPHONE => array(),
        DEVICE_TYPE_MOBILE => array(),
        DEVICE_TYPE_ADMIN => array(),
    );
    public static $arrPluginActions = array();
    public static $arrDeviceType = array(
        DEVICE_TYPE_PC,
        DEVICE_TYPE_SMARTPHONE,
        DEVICE_TYPE_MOBILE,
    );
    public static $arrFormParams = array();
    public static $arrUploadFiles = array();

    /**
     * @var object[]
     */
    public static $arrProvider = array();

    public function install($arrPlugin, SC_Plugin_Installer $objPluginInstaller = null)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $transaction = $objQuery->inTransaction();

        if (!$transaction) {
            $objQuery->begin();
        }

        foreach (static::$arrProvider as $provider) {
            $provider->install($arrPlugin, $objPluginInstaller);
        }

        if (!$transaction) {
            $objQuery->commit();
        }
    }

    public function uninstall($arrPlugin, SC_Plugin_Installer $objPluginInstaller = null)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $transaction = $objQuery->inTransaction();

        if (!$transaction) {
            $objQuery->begin();
        }

        foreach (static::$arrProvider as $provider) {
            $provider->uninstall($arrPlugin, $objPluginInstaller);
        }

        if (!$transaction) {
            $objQuery->commit();
        }
    }

    public function enable($arrPlugin, SC_Plugin_Installer $objPluginInstaller = null)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $transaction = $objQuery->inTransaction();

        if (!$transaction) {
            $objQuery->begin();
        }

        foreach (static::$arrProvider as $provider) {
            $provider->enable($arrPlugin, $objPluginInstaller);
        }

        if (!$transaction) {
            $objQuery->commit();
        }
    }

    public function disable($arrPlugin, SC_Plugin_Installer $objPluginInstaller = null)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $transaction = $objQuery->inTransaction();

        if (!$transaction) {
            $objQuery->begin();
        }

        foreach (static::$arrProvider as $provider) {
            $provider->disable($arrPlugin, $objPluginInstaller);
        }

        if (!$transaction) {
            $objQuery->commit();
        }
    }

    /**
     * 処理の介入箇所とコールバック関数を設定
     * registerはプラグインインスタンス生成時に実行されます
     *
     * @param SC_Helper_Plugin $objHelperPlugin
     */
    public function register(SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        parent::register($objHelperPlugin, $priority);

        foreach (static::$arrProvider as $provider) {
            $provider->register($this, $objHelperPlugin, $priority);
        }

        $objHelperPlugin->addAction("SC_FormParam_construct", array($this, "SC_FormParam_construct"), $priority);
        $objHelperPlugin->addAction("SC_UploadFile_construct", array($this, "SC_UploadFile_construct"), $priority);
        $objHelperPlugin->addAction("loadClassFileChange", array($this, "loadClassFileChange"), $priority);
        $objHelperPlugin->addAction('prefilterTransform', array($this, 'prefilterTransform'), $priority);
        $objHelperPlugin->addAction('outputfilterTransform', array($this, 'outputfilterTransform'), $priority);
    }

    /**
     * @param SC_PluginProvider_Base $objProvider
     */
    public static function addProvider(SC_PluginProvider_Base $objProvider)
    {
        static::$arrProvider[get_class($objProvider)] = $objProvider;
    }

    /**
     * @param string $provider_name
     */
    public static function removeProvider($provider_name)
    {
        unset(static::$arrProvider[$provider_name]);
    }

    /**
     * @param string $provider_name
     * @return object
     */
    public static function getProvider($provider_name)
    {
        return static::$arrProvider[$provider_name];
    }

    /**
     * addFormParam
     *
     * @param string $from_classname
     * @param string $to_classname
     * @param string $classpath
     */
    public static function addFormParam($classname, $func, $priority = 0)
    {
        static::$arrFormParams[$classname][$priority][] = $func;
    }

    /**
     * addUploadFile
     *
     * @param string $from_classname
     * @param string $to_classname
     * @param string $classpath
     */
    public static function addUploadFile($classname, $func, $priority = 0)
    {
        static::$arrUploadFiles[$classname][$priority][] = $func;
    }

    /**
     * addLoadClassFileChange
     *
     * @param string $from_classname
     * @param string $to_classname
     * @param string $classpath
     */
    public static function addLoadClassFileChange($from_classname, $to_classname, $classpath)
    {
        static::$arrLoadClassFileChange[$from_classname] = array($to_classname, $classpath);
    }

    /**
     * addPrefilter
     *
     * @param integer $deviceType
     * @param string $filename
     * @param callable $func
     */
    public static function addPrefilter($deviceType, $filename, $func, $priority = 0)
    {
        static::$arrPrefilters[$deviceType][$priority][] = array($filename, $func);
    }

    /**
     * addOutputfilter
     *
     * @param integer $deviceType
     * @param string $filename
     * @param callable $func
     */
    public static function addOutputfilter($deviceType, $filename, $func, $priority = 0)
    {
        static::$arrOutputfilters[$deviceType][$priority][] = array($filename, $func);
    }

    /**
     * addTemplateDir
     *
     * @param integer $deviceType
     * @param string $dir
     */
    public static function addTemplateDir($deviceType, $dir)
    {
        static::$arrTemplateDirs[$deviceType][] = $dir;
    }

    /**
     * SC_FormParam_construct
     *
     * @param string $classname
     * @param SC_FormParam $objFormParam
     */
    public function SC_FormParam_construct($classname, SC_FormParam $objFormParam)
    {
        if (static::$arrFormParams[$classname]) {
            krsort(static::$arrFormParams[$classname]);
            foreach (static::$arrFormParams[$classname] as $priority => $arrFormParams) {
                foreach ($arrFormParams as $func) {
                    if (call_user_func_array($func, array($objFormParam)) === false) {
                        break 2;
                    }
                }
            }
        }
    }

    /**
     * SC_UploadFile_construct
     *
     * @param string $classname
     * @param SC_FormParam $objFormParam
     */
    public function SC_UploadFile_construct($classname, SC_UploadFile $objFormParam)
    {
        if (static::$arrUploadFiles[$classname]) {
            krsort(static::$arrUploadFiles[$classname]);
            foreach (static::$arrUploadFiles[$classname] as $priority => $arrUploadFiles) {
                foreach ($arrUploadFiles as $func) {
                    if (call_user_func_array($func, array($objFormParam)) === false) {
                        break 2;
                    }
                }
            }
        }
    }

    /**
     * loadClassFileChange
     *
     * @param string $classname
     * @param string $classpath
     */
    public function loadClassFileChange(&$classname, &$classpath)
    {
        if (static::$arrLoadClassFileChange[$classname]) {
            list($classname, $classpath) = static::$arrLoadClassFileChange[$classname];
        }
    }

    /**
     * プレフィルタコールバック関数
     *
     * @param string &$source テンプレートのHTMLソース
     * @param LC_Page_Ex $objPage ページオブジェクト
     * @param string $filename テンプレートのファイル名
     * @return void
     */
    public function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename)
    {
        if (defined('ADMIN_FUNCTION') && ADMIN_FUNCTION) {
            $deviceType = DEVICE_TYPE_ADMIN;
        } else {
            $deviceType = $objPage->arrPageLayout['device_type_id'];
        }

        krsort(static::$arrPrefilters[$deviceType]);
        foreach (static::$arrPrefilters[$deviceType] as $priority => $arrPrefilters) {
            foreach ($arrPrefilters as $arrPrefilter) {
                list($file, $func) = $arrPrefilter;
                if ($file === '' || strpos($filename, $file) !== false) {
                    if (call_user_func_array($func, array(&$source, $objPage, $filename)) === false) {
                        break 2;
                    }
                }
            }
        }
    }

    /**
     * outputfilterTransform
     *
     * @param string &$source テンプレートのHTMLソース
     * @param LC_Page_Ex $objPage ページオブジェクト
     * @param string $filename テンプレートのファイル名
     * @return void
     */
    public function outputfilterTransform(&$source, LC_Page_Ex $objPage, $filename)
    {
        if (defined('ADMIN_FUNCTION') && ADMIN_FUNCTION) {
            $deviceType = DEVICE_TYPE_ADMIN;
        } else {
            $deviceType = $objPage->arrPageLayout['device_type_id'];
        }

        krsort(static::$arrOutputfilters[$deviceType]);
        foreach (static::$arrOutputfilters[$deviceType] as $priority => $arrOutputfilters) {
            foreach ($arrOutputfilters as $arrOutputfilter) {
                list($file, $func) = $arrOutputfilter;
                if (strpos($file === '' || $filename, $file) !== false) {
                    if (call_user_func_array($func, array(&$source, $objPage, $filename)) === false) {
                        break 2;
                    }
                }
            }
        }
    }


    /** Util */
    /**
     * insertMailTemplate
     *
     * @param string $path
     * @param string $name
     */
    public static function insertMailTemplate($path, $name)
    {
        $masterData = new SC_DB_MasterData_Ex();
        $masterData->objQuery = SC_Query_Ex::getSingletonInstance();

        $arrMailTplPath = $masterData->getMasterData('mtb_mail_tpl_path');
        $arrMailTemplate = $masterData->getMasterData('mtb_mail_template');

        $key = array_search($path, $arrMailTplPath);
        if ($key === false) {
            $arrMailTplPath[] = $path;
            $key = array_search($path, $arrMailTplPath);
            $arrMailTemplate[$key] = $name;
        }

        $masterData->deleteMasterData('mtb_mail_tpl_path', false);
        $masterData->registMasterData('mtb_mail_tpl_path', array('id', 'name', 'rank'), $arrMailTplPath, false);
        $masterData->deleteMasterData('mtb_mail_template', false);
        $masterData->registMasterData('mtb_mail_template', array('id', 'name', 'rank'), $arrMailTemplate, false);
    }
    /**
     * insertMailTemplate
     *
     * @param string $path
     * @param string $name
     */
    public static function deleteMailTemplate($path)
    {
        $masterData = new SC_DB_MasterData_Ex();
        $masterData->objQuery = SC_Query_Ex::getSingletonInstance();

        $arrMailTplPath = $masterData->getMasterData('mtb_mail_tpl_path');
        $arrMailTemplate = $masterData->getMasterData('mtb_mail_template');

        $key = array_search($path, $arrMailTplPath);
        if ($key !== false) {
            unset($arrMailTplPath[$key]);
            unset($arrMailTemplate[$key]);
        }

        $masterData->deleteMasterData('mtb_mail_tpl_path', false);
        $masterData->registMasterData('mtb_mail_tpl_path', array('id', 'name', 'rank'), $arrMailTplPath, false);
        $masterData->deleteMasterData('mtb_mail_template', false);
        $masterData->registMasterData('mtb_mail_template', array('id', 'name', 'rank'), $arrMailTemplate, false);
    }

    /**
     * insertPage
     *
     * @param array $sqlval
     */
    public static function insertPage($sqlval)
    {
        $sqlval += array(
            'header_chk' => 1,
            'footer_chk' => 1,
            'edit_flg' => 2,
            'author' => null,
            'description' => null,
            'keyword' => null,
            'update_url' => null,
            'create_date' => 'CURRENT_TIMESTAMP',
            'update_date' => 'CURRENT_TIMESTAMP',
        );

        $objQuery = SC_Query_Ex::getSingletonInstance();

        foreach (static::$arrDeviceType as $device_type) {
            if (!static::existsPage($sqlval['url'], $device_type)) {
                $sqlval['device_type_id'] = $device_type;
                $sqlval['page_id'] = $objQuery->max('page_id', "dtb_pagelayout", "device_type_id = ?", array($device_type)) + 1;
                $objQuery->insert("dtb_pagelayout", $sqlval);
            }
        }
    }

    /**
     * existsPage
     *
     * @param string $url
     * @param integer $device_type
     * @return bool
     */
    public static function existsPage($url, $device_type)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();

        return $objQuery->exists("dtb_pagelayout", "url = ? AND device_type_id = ?", array($url, $device_type));
    }

    /**
     * deletePage
     *
     * @param string $url
     */
    public static function deletePage($url)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->delete("dtb_blocposition", "page_id IN (SELECT p.page_id FROM dtb_pagelayout p WHERE p.url = ?)", array($url));
        $objQuery->delete("dtb_pagelayout", "url = ?", array($url));
    }

    /**
     * copy
     *
     * @param string $src
     * @param string $des
     * @param string $mess
     * @param bool $override
     * @return string|bool
     */
    public static function copy($src, $des, $mess = '', $override = false)
    {
        if (is_dir($src)) {
            return SC_Utils_Ex::sfCopyDir($src, $des, $mess, $override);
        } else {
            return @copy($src, $des);
        }
    }

    /**
     * delete
     *
     * @param string $path
     * @param bool $del_myself
     * @return bool
     */
    public static function delete($path, $del_myself = true)
    {
        return SC_Helper_FileManager_Ex::deleteFile($path, $del_myself);
    }

    /* Plugin */
    /**
     * Utility function to set a hook point.
     *
     * @param  string  $hook_point          hook point
     * @param  SC_SiteView[]   $arrArgs             argument passing to callback function
     * @return void
     */
    public static function hook($hook_point, $arrArgs = array())
    {
        if (!isset(static::$arrPluginActions[$hook_point])) {
            return;
        }

        krsort(static::$arrPluginActions[$hook_point]);
        foreach (static::$arrPluginActions[$hook_point] as $priority => $arrPluginAction) {
            foreach ($arrPluginAction as $plugin_action) {
                if (call_user_func_array($plugin_action, $arrArgs) === false) {
//                    break 2;
                }
            }
        }
    }

    /**
     * プラグイン コールバック関数を追加する
     *
     * @param  string   $hook_point フックポイント名
     * @param  callable $function   コールバック関数名
     * @param  integer   $priority   同一フックポイント内での実行優先度
     * @return boolean  成功すればtrue
     */
    public static function addAction($hook_point, $function, $priority = 0)
    {
        if (!is_callable($function)) {
            return false;
        }

        static::$arrPluginActions[$hook_point][$priority][] = $function;

        return true;
    }

    public static function executePage($page_name)
    {
        $objPage = new $page_name();
        $objPage->init();
        $objPage->process();
        exit;
    }

}
