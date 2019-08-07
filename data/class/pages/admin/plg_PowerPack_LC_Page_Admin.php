<?php

require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * plg_PowserPack_LC_Page_Admin
 *
 * @package PowerPack
 */
class plg_PowerPack_LC_Page_Admin extends LC_Page_Admin_Ex
{
    public $namespace = 'powerpack.';

    public function setFlash($key, $value)
    {
        $_SESSION[$this->namespace . $key] = $value;
    }

    public function getFlash($key, $default = '')
    {
        if (isset($_SESSION[$this->namespace . $key])) {
            $value = $_SESSION[$this->namespace . $key];
            unset($_SESSION[$this->namespace . $key]);
        } else {
            $value = $default;
        }

        return $value;
    }

    public function setFlashAlert($key, $default)
    {
        $message = $this->getFlash($key, $default);
        if ($message) {
            $this->tpl_onload = "alert('" . static::escape_js($message) . "');";
        }
    }

    public static function escape_js($text)
    {
        return substr(json_encode("$text"), 1, -1);
    }
}
