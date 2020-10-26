<?php

/**
 * HttpResponse を扱うクラス.
 *
 * @package PowerPack
 */
class plg_PowerPack_SC_Response extends SC_Response
{

    /**
     * アプリケーション内でリダイレクトする
     *
     * 内部で生成する URL の searchpart は、下記の順で上書きしていく。(後勝ち)
     * 1. 引数 $inheritQueryString が true の場合、$_SERVER['QUERY_STRING']
     * 2. $location に含まれる searchpart
     * 3. 引数 $arrQueryString
     * @param  string    $location           「url-path」「現在のURLからのパス」「URL」のいずれか。「../」の解釈は行なわない。
     * @param  array     $arrQueryString     URL に付加する searchpart
     * @param  bool      $inheritQueryString 現在のリクエストの searchpart を継承するか
     * @param  bool|null $useSsl             true:HTTPSを強制, false:HTTPを強制, null:継承
     * @return void
     * @static
     */
    public function sendRedirect($location, $arrQueryString = array(), $inheritQueryString = false, $useSsl = null)
    {
        PowerPack::hook('SC_Response::sendRedirect', array(&$location, &$arrQueryString, &$inheritQueryString, &$useSsl));

        // ローカルフックポイント処理
        $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($this->plugin_activate_flg);

        if (is_object($objPlugin)) {
            $arrBacktrace = debug_backtrace();
            if (is_object($arrBacktrace[0]['object']) && method_exists($arrBacktrace[0]['object'], 'getMode')) {
                $parent_class_name = get_parent_class($arrBacktrace[0]['object']);
                $objPlugin->doAction($parent_class_name . '_action_' . $arrBacktrace[0]['object']->getMode(), array($arrBacktrace[0]['object']));
                $class_name = get_class($arrBacktrace[0]['object']);
                if ($class_name != $parent_class_name) {
                    $objPlugin->doAction($class_name . '_action_' . $arrBacktrace[0]['object']->getMode(), array($this));
                }
            } elseif (is_object($arrBacktrace[0]['object'])) {
                $pattern = '/^[a-zA-Z0-9_]+$/';
                $mode = null;
                if (isset($_GET['mode']) && preg_match($pattern, $_GET['mode'])) {
                    $mode =  $_GET['mode'];
                } elseif (isset($_POST['mode']) && preg_match($pattern, $_POST['mode'])) {
                    $mode = $_POST['mode'];
                }
                $parent_class_name = get_parent_class($arrBacktrace[0]['object']);
                $objPlugin->doAction($parent_class_name . '_action_' . $mode, array($arrBacktrace[0]['object']));
                $class_name = get_class($arrBacktrace[0]['object']);
                if ($class_name != $parent_class_name) {
                    $objPlugin->doAction($class_name . '_action_' . $mode, array($this));
                }
            }
        }

        // url-path → URL 変換
        if ($location[0] === '/') {
            $netUrl = new Net_URL($location);
            $location = $netUrl->getUrl();
        }

        // URL の場合
        if (preg_match('/^https?:/', $location)) {
            $url = $location;
            if (is_bool($useSsl)) {
                if ($useSsl) {
                    $pattern = '/^' . preg_quote(HTTP_URL, '/') . '(.*)/';
                    $replacement = HTTPS_URL . '\1';
                    $url = preg_replace($pattern, $replacement, $url);
                } else {
                    $pattern = '/^' . preg_quote(HTTPS_URL, '/') . '(.*)/';
                    $replacement = HTTP_URL . '\1';
                    $url = preg_replace($pattern, $replacement, $url);
                }
            }
            // 現在のURLからのパス
        } else {
            if (!is_bool($useSsl)) {
                $useSsl = SC_Utils_Ex::sfIsHTTPS();
            }
            $netUrl = new Net_URL($useSsl ? HTTPS_URL : HTTP_URL);
            $netUrl->path = dirname($_SERVER['SCRIPT_NAME']) . '/' . $location;
            $url = $netUrl->getUrl();
        }

        $pattern = '/^(' . preg_quote(HTTP_URL, '/') . '|' . preg_quote(HTTPS_URL, '/') . ')/';

        // アプリケーション外へのリダイレクトは扱わない
        if (preg_match($pattern, $url) === 0) {
            trigger_error('', E_USER_ERROR);
        }

        $netUrl = new Net_URL($url);

        if ($inheritQueryString && !empty($_SERVER['QUERY_STRING'])) {
            $arrQueryStringBackup = $netUrl->querystring;
            // XXX メソッド名は add で始まるが、実際には置換を行う
            $netUrl->addRawQueryString($_SERVER['QUERY_STRING']);
            $netUrl->querystring = array_merge($netUrl->querystring, $arrQueryStringBackup);
        }

        $netUrl->querystring = array_merge($netUrl->querystring, $arrQueryString);

        $session = SC_SessionFactory_Ex::getInstance();
        if ((SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE)
            || ($session->useCookie() == false)
        ) {
            $netUrl->addQueryString(session_name(), session_id());
        }

        if ($_SERVER["REQUEST_METHOD"] != 'GET') {
            $netUrl->addQueryString(TRANSACTION_ID_NAME, SC_Helper_Session_Ex::getToken());
        }
        $url = $netUrl->getURL();

        header("Location: $url");
        exit;
    }

}
