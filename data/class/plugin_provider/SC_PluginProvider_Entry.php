<?php

define("POWERPACK_ENTRY_KIYAKU_SKIP", true);

/**
 * Class SC_PluginProvider_Entry
 *
 * 規約スキップと登録後に戻るページの設定を可能に
 */
class SC_PluginProvider_Entry extends SC_PluginProvider_Base
{
    public function install($arrPlugin, SC_Plugin_Installer $objPluginInstaller = null)
    {
        PowerPack::insertMailTemplate('mail_templates/customer_mail.tpl', '会員登録のご確認');
        PowerPack::insertMailTemplate('mail_templates/customer_regist_mail.tpl', '会員登録のご完了');
    }

    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        // 規約スキップ
        if (defined('POWERPACK_ENTRY_KIYAKU_SKIP') && POWERPACK_ENTRY_KIYAKU_SKIP) {
            PowerPack::addPrefilter(DEVICE_TYPE_PC, 'entry/index.tpl', function (&$source) {
                $objTransform = new SC_Helper_Transform($source);
                $objTransform->select('.btn_area', 0)->replaceElement('<!--{include file=\'entry/_index_kiyaku.tpl\'}-->');
                $source = $objTransform->getHTML();
            });
        }

        $objHelperPlugin->addAction("LC_Page_Entry_Kiyaku_action_after", array($this, "LC_Page_Entry_Kiyaku_action_after"), $priority);
        $objHelperPlugin->addAction("LC_Page_Entry_action_before", array($this, "LC_Page_Entry_action_before"), $priority);
        $objHelperPlugin->addAction("LC_Page_Regist_Complete_action_after", array($this, "LC_Page_Regist_Complete_action_after"), $priority);
    }

    public function LC_Page_Entry_Kiyaku_action_after(LC_Page_Entry_Kiyaku $objPage)
    {
        if ($_POST['url']) {
            $_SESSION['entry.return_url'] = htmlspecialchars($_POST['url'], ENT_QUOTES);
        } else {
            unset($_SESSION['entry.return_url']);
        }

        if (defined('POWERPACK_ENTRY_KIYAKU_SKIP') && POWERPACK_ENTRY_KIYAKU_SKIP) {
            SC_Response_Ex::sendRedirect('');
            exit;
        }
    }

    public function LC_Page_Entry_action_before(LC_Page_Entry $objPage)
    {
        if (defined('POWERPACK_ENTRY_KIYAKU_SKIP') && POWERPACK_ENTRY_KIYAKU_SKIP) {
            $_SERVER['HTTP_REFERER'] = ENTRY_URL;

            $arrKiyaku = $this->lfGetKiyakuData();
            $objPage->max = count($arrKiyaku);

            // mobile時はGETでページ指定
            if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
                $objPage->offset = $this->lfSetOffset($_GET['offset']);
            }

            $objPage->tpl_kiyaku_text = $this->lfMakeKiyakuText($arrKiyaku, $objPage->max, $objPage->offset);
        }
    }

    /**
     * 規約文の作成
     *
     * @param mixed $arrKiyaku
     * @param integer $max
     * @param mixed $offset
     * @access public
     * @return string 規約の内容をテキストエリアで表示するように整形したデータ
     */
    private function lfMakeKiyakuText($arrKiyaku, $max, $offset)
    {
        $tpl_kiyaku_text = '';
        for ($i = 0; $i < $max; $i++) {
            if ($offset !== null && ($offset - 1) <> $i) continue;
            $tpl_kiyaku_text.=$arrKiyaku[$i]['kiyaku_title'] . "\n\n";
            $tpl_kiyaku_text.=$arrKiyaku[$i]['kiyaku_text'] . "\n\n";
        }

        return $tpl_kiyaku_text;
    }

    /**
     * 規約内容の取得
     *
     * @access private
     * @return array $arrKiyaku 規約の配列
     */
    private function lfGetKiyakuData()
    {
        $objKiyaku = new SC_Helper_Kiyaku_Ex();
        $arrKiyaku = $objKiyaku->getList();

        return $arrKiyaku;
    }

    public function LC_Page_Regist_Complete_action_after(LC_Page_Regist_Complete $objPage)
    {
        if ($_SESSION['entry.return_url']) {
            $url = $_SESSION['entry.return_url'];
            unset($_SESSION['entry.return_url']);

            SC_Response_Ex::sendRedirect($url);
            exit;
        }
    }
}
