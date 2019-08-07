<?php

abstract class SC_PluginProvider_Base
{

    public function install($arrPlugin, SC_Plugin_Installer $objPluginInstaller = null)
    {
    }

    public function uninstall($arrPlugin, SC_Plugin_Installer $objPluginInstaller = null)
    {
    }

    public function enable($arrPlugin, SC_Plugin_Installer $objPluginInstaller = null)
    {
    }

    public function disable($arrPlugin, SC_Plugin_Installer $objPluginInstaller = null)
    {
    }

    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
    }
}
