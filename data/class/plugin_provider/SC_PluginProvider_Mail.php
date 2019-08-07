<?php

/**
 * Class SC_PluginProvider_Mail
 *
 * メールの tpl_header / tpl_footer 未対応を対応
 */
class SC_PluginProvider_Mail extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        PowerPack::addAction('SC_View::fetch', function (SC_View $objView, $template) {
            if (strpos('mail_templates/', $template) === 0) {
                if (!isset($objView->_smarty->_tpl_vars['tpl_header']) && !isset($objView->_smarty->_tpl_vars['tpl_footer'])) {
                    $masterData = new SC_DB_MasterData_Ex();
                    $arrMAILTPLPATH =  $masterData->getMasterData('mtb_mail_tpl_path');

                    $template_id = array_search($template, $arrMAILTPLPATH);
                    if ($template_id) {
                        $objQuery = SC_Query_Ex::getSingletonInstance();
                        $arrMailTemplate = $objQuery->getRow(
                            'subject, header, footer',
                            'dtb_mailtemplate',
                            'template_id = ?',
                            array($template_id)
                        );

                        $this->assign('tpl_header', $arrMailTemplate['header']);
                        $this->assign('tpl_footer', $arrMailTemplate['footer']);
                    }
                }
            }
        }, $priority);
    }
}
