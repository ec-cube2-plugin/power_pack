<?php

require_once CLASS_REALDIR . 'pages/LC_Page_ResizeImage.php';

/**
 * リサイズイメージ のページクラス(拡張).
 *
 * @package PowerPack
 */
class plg_PowerPack_LC_Page_ResizeImage extends LC_Page_ResizeImage
{

    /**
     * 画像の出力
     *
     * @param string  $file   画像ファイル名
     * @param integer $width  画像の幅
     * @param integer $height 画像の高さ
     *
     * @return void
     */
    public function lfOutputImage($file, $width, $height)
    {
        if ($file && $file !== NO_IMAGE_REALDIR) {
            $time = filemtime($file);
            $etag = md5($file . ' ' . $width . ' ' . $height . ' ' . $time);

            if (
                (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $time) &&
                (isset($_SERVER ['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === '"' . $etag . '"')) {
                if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.0') {
                    header("HTTP/1.0 304 Not Modified");
                } else {
                    header("HTTP/1.1 304 Not Modified");
                }
                header('Etag: "' . $etag . '"');
                exit;
            } else {
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $time) . ' GMT');
                header('Etag: "' . $etag . '"');
            }
        }

        parent::lfOutputImage($file, $width, $height);
    }

}
