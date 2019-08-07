<?php

define("POWERPACK_DATA_REALDIR", PLUGIN_UPLOAD_REALDIR . "PowerPack/data/");
define("POWERPACK_HTML_REALDIR", PLUGIN_UPLOAD_REALDIR . "PowerPack/html/");
define("POWERPACK_CLASS_REALDIR", POWERPACK_DATA_REALDIR . "class/");
define("POWERPACK_SMARTY_TEMPLATES_REALDIR", POWERPACK_DATA_REALDIR . "templates/");
define("POWERPACK_TEMPLATE_REALDIR", POWERPACK_SMARTY_TEMPLATES_REALDIR . "default/");
define("POWERPACK_SMARTPHONE_TEMPLATE_REALDIR", POWERPACK_SMARTY_TEMPLATES_REALDIR . "sphone/");
define("POWERPACK_MOBILE_TEMPLATE_REALDIR", POWERPACK_SMARTY_TEMPLATES_REALDIR . "mobile/");
define("POWERPACK_TEMPLATE_ADMIN_REALDIR", POWERPACK_SMARTY_TEMPLATES_REALDIR . "admin/");

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../../../vendor/autoload.php';
}

/**
 * プラグインのメインクラス
 *
 * @package PowerPack
 */
class PowerPack extends PowerPack_Base
{
    public static $arrColumns = array(
        'product' => array(),
        'customer' => array(),
    );

    /**
     * コンストラクタ
     * プラグイン情報(dtb_plugin)をメンバ変数をセットします.
     */
    public function __construct(array $arrSelfInfo)
    {
        $this->arrConfig = unserialize($arrSelfInfo['free_field1']);
        if ($this->arrConfig) {
            foreach ($this->arrConfig as $name => $value) {
                define($name, $value);
            }
        }

        parent::__construct($arrSelfInfo);

        PowerPack::init();
    }

    public function init()
    {
        PowerPack::addProvider(new SC_PluginProvider_Admin_Design());
        PowerPack::addProvider(new SC_PluginProvider_Admin_ProductAction());
        PowerPack::addProvider(new SC_PluginProvider_Admin_ProductSearchMaker());
        PowerPack::addProvider(new SC_PluginProvider_Admin_ProductSearchSort());
        PowerPack::addProvider(new SC_PluginProvider_Admin_Util());

        PowerPack::addProvider(new SC_PluginProvider_Cdn());
        PowerPack::addProvider(new SC_PluginProvider_CustomColumn());
        PowerPack::addProvider(new SC_PluginProvider_CustomerRank());
        PowerPack::addProvider(new SC_PluginProvider_Entry());
        PowerPack::addProvider(new SC_PluginProvider_Forgot());
        PowerPack::addProvider(new SC_PluginProvider_Header());
        PowerPack::addProvider(new SC_PluginProvider_Https());
        PowerPack::addProvider(new SC_PluginProvider_Mail());
        PowerPack::addProvider(new SC_PluginProvider_Maker());
        PowerPack::addProvider(new SC_PluginProvider_News());
        PowerPack::addProvider(new SC_PluginProvider_Path());
        PowerPack::addProvider(new SC_PluginProvider_ProductsAddStatus());
        PowerPack::addProvider(new SC_PluginProvider_ProductsDetailReviewPage());
        PowerPack::addProvider(new SC_PluginProvider_ProductsDetailSelect());
        PowerPack::addProvider(new SC_PluginProvider_ProductsList());
        PowerPack::addProvider(new SC_PluginProvider_ProductsListChildCategory());
        PowerPack::addProvider(new SC_PluginProvider_ProductsListChildMaker());
        PowerPack::addProvider(new SC_PluginProvider_ProductsRequireLogin());
        PowerPack::addProvider(new SC_PluginProvider_ResizeImage());
        PowerPack::addProvider(new SC_PluginProvider_ShoppingCv());
        PowerPack::addProvider(new SC_PluginProvider_SmartyEnableScript());

        PowerPack::addProvider(new SC_PluginProvider_FixPaygent());
    }

    public function install($arrPlugin, SC_Plugin_Installer $objPluginInstaller = null)
    {
        PowerPack::copy(POWERPACK_HTML_REALDIR, HTML_REALDIR);

        PowerPack::init();
        parent::install($arrPlugin, $objPluginInstaller);
    }

    public function uninstall($arrPlugin, SC_Plugin_Installer $objPluginInstaller = null)
    {
        PowerPack::init();
        parent::uninstall($arrPlugin, $objPluginInstaller);

        $deletes = array(
        );
        foreach ($deletes as $delete) {
            PowerPack::delete(HTML_REALDIR . $delete . '/');
            PowerPack::delete(TEMPLATE_REALDIR . $delete . '/');
        }
    }

    public function enable($arrPlugin, SC_Plugin_Installer $objPluginInstaller = null)
    {
        PowerPack::init();
        parent::enable($arrPlugin, $objPluginInstaller);
    }

    public function disable($arrPlugin, SC_Plugin_Installer $objPluginInstaller = null)
    {
        PowerPack::init();
        parent::disable($arrPlugin, $objPluginInstaller);
    }

    /**
     * 処理の介入箇所とコールバック関数を設定
     * registerはプラグインインスタンス生成時に実行されます
     *
     * @param SC_Helper_Plugin $objHelperPlugin
     * @param integer $priority
     */
    public function register(SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        plg_PowerPack_SC_Helper_HandleError::load();

        PowerPack::addLoadClassFileChange("SC_CartSession_Ex", "plg_PowerPack_SC_CartSession", POWERPACK_CLASS_REALDIR . "plg_PowerPack_SC_CartSession.php");
        PowerPack::addLoadClassFileChange("SC_Customer_Ex", "plg_PowerPack_SC_Customer", POWERPACK_CLASS_REALDIR . "plg_PowerPack_SC_Customer.php");
        PowerPack::addLoadClassFileChange("SC_CustomerList_Ex", "plg_PowerPack_SC_CustomerList", POWERPACK_CLASS_REALDIR . "plg_PowerPack_SC_CustomerList.php");
        PowerPack::addLoadClassFileChange("SC_FormParam_Ex", "plg_PowerPack_SC_FormParam", POWERPACK_CLASS_REALDIR . "plg_PowerPack_SC_FormParam.php");
        PowerPack::addLoadClassFileChange("SC_UploadFile_Ex", "plg_PowerPack_SC_UploadFile", POWERPACK_CLASS_REALDIR . "plg_PowerPack_SC_UploadFile.php");
        PowerPack::addLoadClassFileChange("SC_MobileImage_Ex", "plg_PowerPack_SC_MobileImage", POWERPACK_CLASS_REALDIR . "plg_PowerPack_SC_MobileImage.php");
        PowerPack::addLoadClassFileChange("SC_PageNavi_Ex", "plg_PowerPack_SC_PageNavi", POWERPACK_CLASS_REALDIR . "plg_PowerPack_SC_PageNavi.php");
        PowerPack::addLoadClassFileChange("SC_Product_Ex", "plg_PowerPack_SC_Product", POWERPACK_CLASS_REALDIR . "plg_PowerPack_SC_Product.php");
        PowerPack::addLoadClassFileChange("SC_Response_Ex", "plg_PowerPack_SC_Response", POWERPACK_CLASS_REALDIR . "plg_PowerPack_SC_Response.php");
        // View
        PowerPack::addLoadClassFileChange("SC_View_Ex", "plg_PowerPack_SC_View", POWERPACK_CLASS_REALDIR . "plg_PowerPack_SC_View.php");
        PowerPack::addLoadClassFileChange("SC_AdminView_Ex", "plg_PowerPack_SC_AdminView", POWERPACK_CLASS_REALDIR . "plg_PowerPack_SC_AdminView.php");
        PowerPack::addLoadClassFileChange("SC_MobileView_Ex", "plg_PowerPack_SC_MobileView", POWERPACK_CLASS_REALDIR . "plg_PowerPack_SC_MobileView.php");
        PowerPack::addLoadClassFileChange("SC_SiteView_Ex", "plg_PowerPack_SC_SiteView", POWERPACK_CLASS_REALDIR . "plg_PowerPack_SC_SiteView.php");
        PowerPack::addLoadClassFileChange("SC_SmartphoneView_Ex", "plg_PowerPack_SC_SmartphoneView", POWERPACK_CLASS_REALDIR . "plg_PowerPack_SC_SmartphoneView.php");
        // Helper
        PowerPack::addLoadClassFileChange("SC_Helper_Customer_Ex", "plg_PowerPack_SC_Helper_Customer", POWERPACK_CLASS_REALDIR . "helper/plg_PowerPack_SC_Helper_Customer.php");
        PowerPack::addLoadClassFileChange("SC_Helper_Purchase_Ex", "plg_PowerPack_SC_Helper_Purchase", POWERPACK_CLASS_REALDIR . "helper/plg_PowerPack_SC_Helper_Purchase.php");

        PowerPack::addTemplateDir(DEVICE_TYPE_ADMIN, POWERPACK_TEMPLATE_ADMIN_REALDIR);
        PowerPack::addTemplateDir(DEVICE_TYPE_PC, POWERPACK_TEMPLATE_REALDIR);
        PowerPack::addTemplateDir(DEVICE_TYPE_SMARTPHONE, POWERPACK_SMARTPHONE_TEMPLATE_REALDIR);
        PowerPack::addTemplateDir(DEVICE_TYPE_MOBILE, POWERPACK_MOBILE_TEMPLATE_REALDIR);

        parent::register($objHelperPlugin, $priority);

        // LC_Page
        $objHelperPlugin->addAction("LC_Page_Admin_Products_Ex_action_before", array($this, "LC_Page_Admin_Products_action_before"), $priority);
        $objHelperPlugin->addAction("LC_Page_Admin_Products_Product_action_after", array($this, "LC_Page_Admin_Products_Product_action_after"), $priority);
//        $objHelperPlugin->addAction("LC_Page_Admin_Products_ProductClass_Ex_action_before", array($this, "LC_Page_Admin_Products_ProductClass_action_before"), $priority);
    }

    public function LC_Page_Admin_Products_action_before(LC_Page_Admin_Products $objPage)
    {
        $objPage = new plg_PowerPack_LC_Page_Admin_Products();
        $objPage->init();
        $objPage->process();
        exit();
    }

    public function LC_Page_Admin_Products_Product_action_after(LC_Page_Admin_Products_Product $objPage)
    {
        PowerPack::hook('LC_Page_Admin_Products_Product::action_after', array($objPage));
    }
}
