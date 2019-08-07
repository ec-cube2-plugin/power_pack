<?php

/**
 * エラーハンドリングのクラス
 *
 * 依存するクラスに構文エラーがあると、捕捉できない。よって、依存は最小に留めること。
 * 現状 GC_Utils_Ex(GC_Utils) に依存しているため、その中で構文エラーは捕捉できない。
 * @package PowerPack
 * @version $Id$
 */
class plg_PowerPack_SC_Helper_HandleError extends SC_Helper_HandleError
{
    /** エラー処理中か */
    static $under_error_handling = false;

    /**
     * 処理の読み込みを行う
     *
     * @return void
     */
    public static function load()
    {
        if (!(defined('SAFE') && SAFE === true) && !(defined('INSTALL_FUNCTION') && INSTALL_FUNCTION === true)) {
            set_error_handler(array(__CLASS__, 'error_handler'), E_USER_ERROR | E_WARNING | E_USER_WARNING | E_CORE_WARNING | E_COMPILE_WARNING);
            set_exception_handler(array(__CLASS__, 'exception_handler'));
        }
    }

    /**
     * exception_handler
     *
     * @param Exception $ex
     */
    public static function exception_handler(Exception $ex)
    {
        static::ob();

        if ($ex instanceof NotFoundException) {
            require_once CLASS_EX_REALDIR . 'page_extends/error/LC_Page_Error_SystemError_Ex.php';
            $objPage = new LC_Page_Error_SystemError_Ex();
            $objPage->init();

            SC_Response_Ex::sendHttpStatus(404);
            $objPage->tpl_title = '404 NotFound';
            $objPage->tpl_error = 'ご指定のページはございません。';
            $objPage->sendResponse();
            exit;
        } else {
            throw $ex;
        }
    }

    /**
     * 警告や E_USER_ERROR を捕捉した場合にエラー画面を表示させるエラーハンドラ関数.
     *
     * この関数は, set_error_handler() 関数に登録するための関数である.
     * trigger_error にて E_USER_ERROR が生成されると, エラーログを出力した後,
     * エラー画面を表示させる.
     * E_WARNING, E_USER_WARNING が発生した場合、ログを記録して、true を返す。
     * (エラー画面・エラー文言は表示させない。)
     *
     * @param  integer      $errno   エラーコード
     * @param  string       $errstr  エラーメッセージ
     * @param  string       $errfile エラーが発生したファイル名
     * @param  integer      $errline エラーが発生した行番号
     * @return void|boolean E_USER_ERROR が発生した場合は, エラーページへリダイレクト;
     *                      E_WARNING, E_USER_WARNING が発生した場合、true を返す
     */
    public static function error_handler($errno, $errstr, $errfile, $errline)
    {
        return static::handle_warning($errno, $errstr, $errfile, $errline);
    }

    /**
     * ob
     */
    public static function ob()
    {
        SC_Helper_HandleError_Ex::$under_error_handling = true;

        ob_clean();

        // 絵文字変換・除去フィルターが有効か評価する。
        $loaded_ob_emoji = false;
        $arrObs = ob_get_status(true);
        foreach ($arrObs as $arrOb) {
            if ($arrOb['name'] === 'SC_MobileEmoji::handler') {
                $loaded_ob_emoji = true;
                break;
            }
        }

        // 絵文字変換・除去フィルターが無効で、利用できる場合、有効にする。
        if (!$loaded_ob_emoji && class_exists('SC_MobileEmoji')) {
            ob_start(array('SC_MobileEmoji', 'handler'));
        }
    }
}
