<?php

/**
 * プラグイン の情報クラス.
 *
 * @package PowerPack
 */
class plugin_info
{

    /** プラグインコード(必須)：プラグインを識別する為キーで、他のプラグインと重複しない一意な値である必要がありま. */
    static $PLUGIN_CODE = "PowerPack";

    /** プラグイン名(必須)：EC-CUBE上で表示されるプラグイン名. */
    static $PLUGIN_NAME = "PowerPack for EC-CUBE2";

    /** クラス名(必須)：プラグインのクラス（拡張子は含まない） */
    static $CLASS_NAME = "PowerPack";

    /** プラグインバージョン(必須)：プラグインのバージョン. */
    static $PLUGIN_VERSION = "1.0.0";

    /** 対応バージョン(必須)：対応するEC-CUBEバージョン. */
    static $COMPLIANT_VERSION = "2.13.5";

    /** 作者(必須)：プラグイン作者. */
    static $AUTHOR = "Tsuyoshi Tsurushima";

    /** AUTHOR_SITE_URL */
    static $AUTHOR_SITE_URL = "";

    /** 説明(必須)：プラグインの説明. */
    static $DESCRIPTION = "EC-CUBE2の機能を強化します。";

    /** プラグインURL：プラグイン毎に設定出来るURL（説明ページなど） */
    static $PLUGIN_SITE_URL = "";

    /** ライセンス */
    static $LICENSE = "LGPL";

    /** フックポイント：フックポイントとコールバック関数を定義します */
    static $HOOK_POINTS = array(
    );

}
