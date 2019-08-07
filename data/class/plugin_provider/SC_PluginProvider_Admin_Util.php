<?php

/**
 * Class SC_PluginProvider_Admin_Util
 *
 * 管理画面の挙動改善
 * テキストエリアが自動でサイズが変化
 * page_max のデフォルトを設定通りのデフォルトに
 */
class SC_PluginProvider_Admin_Util extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        // page_max の修正
        PowerPack::addPrefilter(DEVICE_TYPE_ADMIN, '', function(&$source) {
            $source = str_replace('<!--{html_options options=$arrPageMax selected=$arrForm.search_page_max.value}-->', '<!--{html_options options=$arrPageMax selected=$arrForm.search_page_max.value|default:$smarty.const.SEARCH_PMAX}-->', $source);
            $source = str_replace('<!--{html_options options=$arrPageMax selected=$arrForm.search_page_max}-->', '<!--{html_options options=$arrPageMax selected=$arrForm.search_page_max|default:$smarty.const.SEARCH_PMAX}-->', $source);
            $source = str_replace('<!--{html_options options=$arrPageMax selected=$arrForm[$key].value}-->', '<!--{html_options options=$arrPageMax selected=$arrForm[$key].value|default:$smarty.const.SEARCH_PMAX}-->', $source);
        });

        // textarea
        PowerPack::addPrefilter(DEVICE_TYPE_ADMIN, 'main_frame.tpl', function(&$source) {
            $source = str_replace('</body>', '<!--{include file=\'_main_frame_autosize.tpl\'}--></body>', $source);
            $source = str_replace('</body>', '<!--{include file=\'_main_frame_ace.tpl\'}--></body>', $source);
        });

        // ログイン済みの場合 home にリダイレクト
        $objHelperPlugin->addAction('LC_Page_Admin_Index_action_before', array($this, 'LC_Page_Admin_Index_action_before'));
    }

    public function LC_Page_Admin_Index_action_before(LC_Page_Admin_Index $objPage)
    {
        $objSession = new SC_Session_Ex();
        if ($objSession->IsSuccess() === SUCCESS) {
            SC_Response_Ex::sendHttpStatus(302);
            SC_Response_Ex::sendRedirect('./home.php');
        }
    }
}
