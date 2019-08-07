<?php

class plg_PowerPack_SC_SmartphoneView extends SC_SmartphoneView
{
    public function init()
    {
        parent::init();

        $this->_smarty->template_dir = array_merge((array) $this->_smarty->template_dir, PowerPack::$arrTemplateDirs[DEVICE_TYPE_SMARTPHONE]);
    }
}
