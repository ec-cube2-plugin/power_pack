<?php

/**
 * Class SC_PluginProvider_FixPaygent
 *
 * ペイジェントのテンプレートをカスタマイズ可能に
 */
class SC_PluginProvider_FixPaygent extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        $objHelperPlugin->addAction("LC_Page_process", function (LC_Page $objPage) {
            // mdl_paygent対策
            if (strpos($objPage->tpl_mainpage, MODULE_REALDIR . MDL_PAYGENT_CODE) === 0) {
                switch(SC_Display_Ex::detectDevice()) {
                    case DEVICE_TYPE_MOBILE :
                        $file = str_replace(
                            MODULE_REALDIR . MDL_PAYGENT_CODE . '/templates/mobile/',
                            'mdl_paygent/', $objPage->tpl_mainpage
                        );
                        if (file_exists(MOBILE_TEMPLATE_REALDIR . '/' . $file)) {
                            $objPage->tpl_mainpage = $file;
                        }
                        break;
                    case DEVICE_TYPE_SMARTPHONE :
                        $file = str_replace(
                            MODULE_REALDIR . MDL_PAYGENT_CODE . '/templates/sphone/',
                            'mdl_paygent/', $objPage->tpl_mainpage
                        );
                        if (file_exists(SMARTPHONE_TEMPLATE_REALDIR . '/' . $file)) {
                            $objPage->tpl_mainpage = $file;
                        }
                        break;
                    case DEVICE_TYPE_PC :
                    default:
                        $file = str_replace(
                            MODULE_REALDIR . MDL_PAYGENT_CODE . '/templates/default/',
                            'mdl_paygent/', $objPage->tpl_mainpage
                        );
                        if (file_exists(TEMPLATE_REALDIR . '/' . $file)) {
                            $objPage->tpl_mainpage = $file;
                        }
                        break;
                }
            }
        }, $priority);
    }
}
