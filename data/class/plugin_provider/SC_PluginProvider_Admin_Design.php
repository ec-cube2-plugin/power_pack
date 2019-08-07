<?php

/**
 * Class SC_PluginProvider_Admin_Design
 *
 * 管理画面＞デザイン管理にパス・プレビューリンクを追加
 */
class SC_PluginProvider_Admin_Design extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        PowerPack::addPrefilter(DEVICE_TYPE_ADMIN, 'design/index.tpl', function(&$source) {
            $objTransform = new SC_Helper_Transform($source);
            $objTransform->select('table.list', 0, false)->appendFirst('<col width="30%"><col width="30%"><col width="10%"><col width="10%"><col width="10%"><col width="10%">');
            $objTransform->select('table.list th', 0, false)->insertBefore('<th>PATH</th>');
            $objTransform->select('table.list th', 0, false)->insertAfter('<th>プレビュー</th>');
            $objTransform->select('table.list td', 0, false)->insertBefore('<td><!--{$item.url}--></td>');
            $objTransform->select('table.list td', 0, false)->insertAfter('<td class="center"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$item.url}-->" target="_blank">プレビュー</a></td>');
            $source = $objTransform->getHTML();
        });
        PowerPack::addPrefilter(DEVICE_TYPE_ADMIN, 'design/main_edit.tpl', function(&$source) {
            $objTransform = new SC_Helper_Transform($source);
            $objTransform->select('table.list col', 0, false)->replaceElement('<col width="30%"><col width="30%"><col width="10%">');
            $objTransform->select('table.list th', 0, false)->insertBefore('<th>PATH</th>');
            $objTransform->select('table.list th', 0, false)->insertAfter('<th>プレビュー</th>');
            $objTransform->select('table.list td', 0, false)->insertBefore('<td><!--{$item.url}--></td>');
            $objTransform->select('table.list td', 0, false)->insertAfter('<td class="center"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$item.url}-->" target="_blank">プレビュー</a></td>');
            $source = $objTransform->getHTML();
        });

        $objHelperPlugin->addAction("LC_Page_Admin_Design_action_after", array($this, "LC_Page_Admin_Design_action_after"), $priority);
        $objHelperPlugin->addAction("LC_Page_Admin_Design_MainEdit_action_after", array($this, "LC_Page_Admin_Design_MainEdit_action_after"), $priority);
    }

    public function LC_Page_Admin_Design_action_after(LC_Page_Admin_Design $objPage)
    {
        if ($objPage->arrEditPage) {
            usort($objPage->arrEditPage, function ($a, $b) {
                $_a = preg_replace('|/index$|', '/', $a['filename']);
                $_b = preg_replace('|/index$|', '/', $b['filename']);
                return strcmp(substr_count($_a, '/') . '_' . $_a, substr_count($_b, '/') . '_' . $_b);
            });
        }
    }

    public function LC_Page_Admin_Design_MainEdit_action_after(LC_Page_Admin_Design_MainEdit $objPage)
    {
        if ($objPage->arrPageList) {
            usort($objPage->arrPageList, function ($a, $b) {
                $_a = preg_replace('|/index$|', '/', $a['filename']);
                $_b = preg_replace('|/index$|', '/', $b['filename']);
                return strcmp(substr_count($_a, '/') . '_' . $_a, substr_count($_b, '/') . '_' . $_b);
            });
        }
    }
}
