<?php

/**
 * Class SC_PluginProvider_SmartyEnableScript
 *
 * SC_PluginProvider_Smarty で Script に対応
 */
class SC_PluginProvider_SmartyEnableScript extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        PowerPack::addAction('SC_View::init', function (SC_View $objView) {
            $objView->_smarty->default_modifiers = array();
        }, $priority);
    }
}
