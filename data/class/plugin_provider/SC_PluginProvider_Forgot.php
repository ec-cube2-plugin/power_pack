<?php

/**
 * Class SC_PluginProvider_Forgot
 *
 * パスワードを忘れた方を同じウィンドウで開けるように
 */
class SC_PluginProvider_Forgot extends SC_PluginProvider_Base
{
    public function install($arrPlugin, SC_Plugin_Installer $objPluginInstaller = null)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->query("
            CREATE TABLE plg_PowerPack_dtb_forgot (
                id int,
                customer_id varchar(12) NOT NULL,
                expire timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                secret_key text,
                del_flg smallint NOT NULL DEFAULT 0,
                PRIMARY KEY (id)
            );
        ");
    }

    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        // パスワード再発行
        $objHelperPlugin->addAction("LC_Page_Forgot_action_before", function (LC_Page_Forgot $objPage) {
            if (defined('POWERPACK_FORGOT_LAYOUT') && POWERPACK_FORGOT_LAYOUT == 1) {
                $objPage->skip_load_page_layout = false;
                $layout = new SC_Helper_PageLayout_Ex();
                $layout->sfGetPageLayout($objPage, false, $_SERVER['SCRIPT_NAME'], $objPage->objDisplay->detectDevice());
            }
            $objPage->tpl_title = 'パスワードの再発行';
        }, $priority);
        $objHelperPlugin->addAction("LC_Page_Forgot_action_after", array($this, "LC_Page_Forgot_action_after"), $priority);

        // 「パスワードを忘れた時のヒント」削除
        if (defined('POWERPACK_FORGOT_MODE') && POWERPACK_FORGOT_MODE == 1) {
            PowerPack::addPrefilter(DEVICE_TYPE_PC, 'frontparts/form_personal_input.tpl', function (&$source) {
                $objTransform = new SC_Helper_Transform($source);
                $objTransform->select('tr', 15)->removeElement();
                $source = $objTransform->getHTML();
            });
            PowerPack::addPrefilter(DEVICE_TYPE_PC, 'frontparts/form_personal_confirm.tpl', function (&$source) {
                $objTransform = new SC_Helper_Transform($source);
                $objTransform->select('tr', 15)->removeElement();
                $source = $objTransform->getHTML();
            });
            PowerPack::addAction('SC_Helper_Customer::sfCustomerRegisterParam', function (SC_FormParam_Ex $objFormParam, $isAdmin, $is_mypage, $prefix) {
                $objFormParam->removeParam('reminder');
                $objFormParam->removeParam('reminder_answer');
            });
        }

        // 同一ウィンドウ
        if (defined('POWERPACK_FORGOT_LAYOUT') && POWERPACK_FORGOT_LAYOUT == 1) {
            $objHelperPlugin->addAction("LC_Page_Forgot_action_after", function (LC_Page_Forgot $objPage) {
                    $objPage->setTemplate(SITE_FRAME);
            }, $priority);

            PowerPack::addPrefilter(DEVICE_TYPE_PC, 'forgot/index.tpl', function(&$source) {
                $source = str_replace('<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="パスワードを忘れた方(入力ページ)"}-->', '<!--{if !$disable_wincol}--><div id="windowcolumn"><!--{/if}-->', $source);
                $source = str_replace('<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->', '</div>', $source);
            });
            PowerPack::addPrefilter(DEVICE_TYPE_PC, 'forgot/secret.tpl', function(&$source) {
                $source = str_replace('<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="パスワードを忘れた方(確認ページ)"}-->', '<!--{if !$disable_wincol}--><div id="windowcolumn"><!--{/if}-->', $source);
                $source = str_replace('<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->', '</div>', $source);
            });
            PowerPack::addPrefilter(DEVICE_TYPE_PC, 'forgot/complete.tpl', function(&$source) {
                $source = str_replace('<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="パスワードを忘れた方(完了ページ)"}-->', '<!--{if !$disable_wincol}--><div id="windowcolumn"><!--{/if}-->', $source);
                $source = str_replace('<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->', '</div>', $source);
            });

            PowerPack::addPrefilter(DEVICE_TYPE_PC, 'forgot_url/index.tpl', function(&$source) {
                $source = str_replace('<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="パスワードを忘れた方(入力ページ)"}-->', '<!--{if !$disable_wincol}--><div id="windowcolumn"><!--{/if}-->', $source);
                $source = str_replace('<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->', '</div>', $source);
            });
            PowerPack::addPrefilter(DEVICE_TYPE_PC, 'forgot_url/change.tpl', function(&$source) {
                $source = str_replace('<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="パスワードを忘れた方(新しいパスワードの入力)"}-->', '<!--{if !$disable_wincol}--><div id="windowcolumn"><!--{/if}-->', $source);
                $source = str_replace('<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->', '</div>', $source);
            });
            PowerPack::addPrefilter(DEVICE_TYPE_PC, 'forgot_url/change_complete.tpl', function(&$source) {
                $source = str_replace('<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="パスワードを忘れた方(完了ページ)"}-->', '<!--{if !$disable_wincol}--><div id="windowcolumn"><!--{/if}-->', $source);
                $source = str_replace('<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->', '</div>', $source);
                $objTransform = new SC_Helper_Transform($source);
                $objTransform->select('.btn_area')->removeElement();
                $source = $objTransform->getHTML();
            });
            PowerPack::addPrefilter(DEVICE_TYPE_PC, 'forgot_url/mail_complete.tpl', function(&$source) {
                $source = str_replace('<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="パスワードを忘れた方"}-->', '<!--{if !$disable_wincol}--><div id="windowcolumn"><!--{/if}-->', $source);
                $source = str_replace('<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->', '</div>', $source);
                $objTransform = new SC_Helper_Transform($source);
                $objTransform->select('.btn_area')->removeElement();
                $source = $objTransform->getHTML();
            });
            PowerPack::addPrefilter(DEVICE_TYPE_PC, 'forgot_url/error.tpl', function(&$source) {
                $source = str_replace('<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="パスワードを忘れた方(エラー)"}-->', '<!--{if !$disable_wincol}--><div id="windowcolumn"><!--{/if}-->', $source);
                $source = str_replace('<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->', '</div>', $source);
                $objTransform = new SC_Helper_Transform($source);
                $objTransform->select('.btn_area')->removeElement();
                $source = $objTransform->getHTML();
            });

            PowerPack::addPrefilter(DEVICE_TYPE_PC, '', function(&$source) {
                $source = str_replace(' onclick="eccube.openWindow(\'<!--{$smarty.const.HTTPS_URL}-->forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->\',\'forget\',\'600\',\'460\',{scrollbars:\'no\',resizable:\'no\'}); return false;" target="_blank"', '', $source);
            });
        }


        // 設定
        $objHelperPlugin->addAction("LC_Page_Admin_PowerPack_Config_action_before", function (LC_Page_Admin_PowerPack_Config $objPage) {
            $objPage->arrConfigPath[] = 'form/_config_forgot.tpl';
        }, $priority);
        PowerPack::addAction('LC_Form_Admin_PowerPack_Config::__construct', function (plg_PowerPack_SC_FormParam $objFormParam) {
            $objFormParam
                ->add('POWERPACK_FORGOT_MODE', 'radio', array(
                    'label' => '形式',
                    'max_length' => INT_LEN,
                    'required' => true,
                    'convert' => 'n',
                    'constraints' => array('MAX_LENGTH_CHECK', 'NUM_CHECK'),
                    'input_db' => true,
                    'choices' => array('0' => '秘密の質問 (デフォルト)', '1' => 'URLをメールで送信'),
                ))
                ->add('POWERPACK_FORGOT_LAYOUT', 'radio', array(
                    'label' => 'レイアウト',
                    'max_length' => INT_LEN,
                    'required' => true,
                    'convert' => 'n',
                    'constraints' => array('MAX_LENGTH_CHECK', 'NUM_CHECK'),
                    'input_db' => true,
                    'choices' => array('0' => '別ウィンドウ (デフォルト)', '1' => '同一ウィンドウ'),
                ));
        }, $priority);
        PowerPack::addAction('LC_Page_Admin_PowerPack_Config::changeMasterData', function (LC_Page_Admin_PowerPack_Config $objPage, $arrForm) {
            // パスワード再発行方法
            if ($arrForm['POWERPACK_FORGOT_MODE'] == 1) {
                PowerPack::insertMailTemplate('mail_templates/forgot_mail_url.tpl', 'パスワード再発行URL');
                PowerPack::insertMailTemplate('mail_templates/forgot_mail_complete.tpl', 'パスワード再発行完了');
            } else {
                PowerPack::deleteMailTemplate('mail_templates/forgot_mail_url.tpl');
                PowerPack::deleteMailTemplate('mail_templates/forgot_mail_complete.tpl');
            }

            // パスワード再発行を同じ画面で開くか
            if ($arrForm['POWERPACK_FORGOT_LAYOUT'] == 1) {
                PowerPack::insertPage(array(
                    'page_name'     => 'パスワードの再発行',
                    'url'           => 'forgot/index.php',
                    'filename'      => 'forgot/index',
                ));
            } else {
                PowerPack::deletePage('forgot/index.php');
            }
        }, $priority);
    }

    public function LC_Page_Forgot_action_after(LC_Page_Forgot $objPage)
    {
        if (!defined('POWERPACK_FORGOT_MODE') || POWERPACK_FORGOT_MODE != 1) {
            return;
        }

        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();

        switch ($objPage->getMode()) {
            case 'mail_check':
                $objPage->lfInitMailCheckParam($objFormParam, $objPage->device_type);
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $objFormParam->toLower('email');
                $objPage->arrForm = $objFormParam->getHashArray();
                $objPage->arrErr = $objFormParam->checkError();
                if (SC_Utils_Ex::isBlank($objPage->arrErr)) {
                    $objPage->errmsg = $objPage->lfCheckForgotMail($objPage->arrForm, $objPage->arrReminder);
                    if (SC_Utils_Ex::isBlank($objPage->errmsg)) {
                        $this->lfSendForgotMail($objPage->arrForm);
                        SC_Response_Ex::sendRedirect('', array('mode' => 'mail_complete'));
                        SC_Response_Ex::actionExit();
                    }
                }
                break;

            case 'mail_complete':
                $objPage->tpl_mainpage = 'forgot_url/mail_complete.tpl';
                break;

            case 'change':
                // シークレットキーチェック
                $customer_id = $this->lfCheckSecretKey($_GET['key']);
                // シークレットキー一致
                if ($customer_id) {
                    $objPage->tpl_mainpage = 'forgot_url/change.tpl';
                } else {
                    // 入力値エラー
                    $objPage->tpl_mainpage = 'forgot_url/error.tpl';
                    break;
                }

                $this->lfInitPasswordCheckParam($objFormParam);
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $objPage->arrForm = $objFormParam->getHashArray();

                break;

            case 'change_confirm':
                $customer_id = $this->lfCheckSecretKey($_POST['key']);

                // 入力値エラー
                if (!$customer_id) {
                    $objPage->tpl_mainpage = 'forgot_url/error.tpl';
                    break;
                }

                $this->lfInitPasswordCheckParam($objFormParam);
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $objPage->arrForm = $objFormParam->getHashArray();
                $objPage->arrErr = $this->lfErrorCheck($objFormParam);
                if (SC_Utils_Ex::isBlank($objPage->arrErr)) {
                    // 新しいパスワードを設定する
                    $sqlval = array();
                    $sqlval['password'] = $objPage->arrForm['password'];
                    SC_Helper_Customer_Ex::sfEditCustomerData($sqlval, $customer_id);

                    $objQuery = SC_Query_Ex::getSingletonInstance();
                    $objQuery->update('plg_PowerPack_dtb_forgot', array('del_flg' => 1), 'customer_id = ?', array($customer_id));

                    // メールで変更通知をする
                    if (FORGOT_MAIL == 1) {
                        $arrCustomer = SC_Helper_Customer_Ex::sfGetCustomerData($customer_id);
                        $objDb = new SC_Helper_DB_Ex();
                        $CONF = $objDb->sfGetBasisData();
                        $objPage->lfSendMailComplete($CONF, $arrCustomer);
                    }

                    // 完了ページへ移動する
                    SC_Response_Ex::sendRedirect('', array('mode' => 'change_complete'));
                    SC_Response_Ex::actionExit();
                } else {
                    // 入力値エラー
                    $objPage->tpl_mainpage = 'forgot_url/change.tpl';
                }
                break;

            case 'change_complete':
                // 完了ページ
                $objPage->tpl_mainpage = 'forgot_url/change_complete.tpl';
                break;

            case '':
                $objPage->tpl_mainpage = 'forgot_url/index.tpl';
                break;

            default:
                SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
                break;
        }
    }

    /**
     * メールアドレス・名前確認
     *
     * @param  array  $arrForm     フォーム入力値
     * @param  array  $arrReminder リマインダー質問リスト
     */
    public function lfSendForgotMail(&$arrForm)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

        //
        $arrCustomer = $objQuery->getRow(
            '*',
            'dtb_customer',
            '(email = ? OR email_mobile = ?) AND name01 = ? AND name02 = ? AND del_flg = 0',
            array($arrForm['email'], $arrForm['email'], $arrForm['name01'], $arrForm['name02'])
        );

        // 現状発行を無効化
        $objQuery->update('plg_PowerPack_dtb_forgot', array('del_flg' => 1), 'customer_id = ?', array($arrCustomer['customer_id']));

        $secret_key = SC_Utils_Ex::sfGetRandomString(40);
        $arrForgot['id'] = $objQuery->nextVal('dtb_forgot_forgot_id');
        $arrForgot['customer_id'] = $arrCustomer['customer_id'];
        $arrForgot['expire'] = date('Y-m-d H:i:s', strtotime('+30 min'));
        $arrForgot['secret_key'] = $secret_key;
        $arrForgot['del_flg'] = 0;
        $objQuery->insert('plg_PowerPack_dtb_forgot', $arrForgot);

        $objQuery->commit();


        // メールでURLを送信
        $objDb = new SC_Helper_DB_Ex();
        $CONF = $objDb->sfGetBasisData();

        // arrMAILTPLPATH
        $masterData = new SC_DB_MasterData_Ex();
        $arrMAILTPLPATH =  $masterData->getMasterData('mtb_mail_tpl_path');
        $template_id = array_search('mail_templates/forgot_mail_url.tpl', $arrMAILTPLPATH);

        // メールテンプレート情報の取得
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $arrMailTemplate = $objQuery->getRow('subject, header, footer', 'dtb_mailtemplate', 'template_id = ?', array($template_id));

        // パスワード変更お知らせメール送信
        $objMailText = new SC_SiteView_Ex(false);
        $objMailText->assign('tpl_header', $arrMailTemplate['header']);
        $objMailText->assign('tpl_footer', $arrMailTemplate['footer']);
        $objMailText->assign('secret_key', $secret_key);
        $objMailText->assign('arrCustomer', $arrCustomer);
        $toCustomerMail = $objMailText->fetch($arrMAILTPLPATH[$template_id]);

        $objHelperMail = new SC_Helper_Mail_Ex();

        // メール送信
        $objMail = new SC_SendMail();
        $objMail->setItem(
            '' //宛先
            , $objHelperMail->sfMakeSubject($arrMailTemplate['subject'])
            , $toCustomerMail //本文
            , $CONF['email03'] //配送元アドレス
            , $CONF['shop_name'] // 配送元名
            , $CONF['email03'] // reply to
            , $CONF['email04'] //return_path
            , $CONF['email04'] // errors_to
        );
        $objMail->setTo($arrForm['email'], $arrCustomer['name01'] . ' ' . $arrCustomer['name02'] . ' 様');
        $objMail->sendMail();
    }

    /**
     * シークレットキー確認
     *
     * @param array $arrForm フォーム入力値
     * @return integer customer_id
     */
    function lfCheckSecretKey($secret_key)
    {
        // 再発行情報取得
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->setOrder('expire DESC');
        $arrForget = $objQuery->getRow(
            '*',
            'plg_PowerPack_dtb_forgot',
            'secret_key = ? AND expire > NOW() AND del_flg = 0',
            array($secret_key)
        );
        if (!$arrForget) {
            return;
        }

        // 顧客情報
        $arrCustomer = SC_Helper_Customer_Ex::sfGetCustomerData($arrForget['customer_id']);
        if (!$arrCustomer) {
            return;
        } else {
            return $arrCustomer['customer_id'];
        }
    }

    /**
     * 秘密の質問確認におけるパラメーター情報の初期化
     *
     * @param array $objFormParam フォームパラメータークラス
     * @param array $device_type デバイスタイプ
     * @return void
     */
    function lfInitPasswordCheckParam(&$objFormParam)
    {
        $objFormParam->addParam('パスワード', 'password', STEXT_LEN, 'a', array('EXIST_CHECK', 'SPTAB_CHECK', 'ALNUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('パスワード(確認)', 'password02', STEXT_LEN, 'a', array('EXIST_CHECK', 'SPTAB_CHECK' ,'ALNUM_CHECK'), '', false);
    }

    /**
     * パスワードチェック
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return SC_CheckError $objErr エラー情報
     */
    function lfErrorCheck(&$objFormParam)
    {
        $objFormParam->convParam();
        $arrParams = $objFormParam->getHashArray();

        // 入力データを渡す。
        $objErr = new SC_CheckError_Ex($arrParams);
        $objErr->arrErr = $objFormParam->checkError();

        $objErr->doFunc(array('パスワード', 'password', PASSWORD_MIN_LEN, PASSWORD_MAX_LEN) ,array('SPTAB_CHECK', 'NUM_RANGE_CHECK'));
        $objErr->doFunc(array('パスワード', 'パスワード(確認)', 'password', 'password02') ,array('EQUAL_CHECK'));

        return $objErr->arrErr;
    }

}
