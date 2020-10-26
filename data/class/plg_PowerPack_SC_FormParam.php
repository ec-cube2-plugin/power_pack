<?php

/**
 * SC_FormParam の拡張
 *
 * @package PowerPack
 */
class plg_PowerPack_SC_FormParam extends SC_FormParam implements ArrayAccess
{

    /**
     * @var string テーブル名
     */
    public $table_name = null;

    /**
     * @var string 主キー名
     */
    public $primary_key = null;

    /**
     * @var SC_UploadFile_Ex オブジェクト
     */
    public $objUpFile;

    public $arrScaleImage = array();

    public $arrSuffix = array();

    public $image_key = 'image_key';

    /**
     * @var array フォームタイプ
     */
    public $type = array();

    /**
     * @var array 日付
     */
    public $arrDate = array();

    /**
     * @var array エラー
     */
    public $arrErr = array();

    /**
     * @var array option
     */
    public $arrOptions = array();

    public $validate = false;

    public function __construct()
    {
        $this->check_dir = IMAGE_SAVE_REALDIR;

        // SC_FormParamのフックポイント
        // TODO: debug_backtrace以外にいい方法があれば良いが、一旦これで
        $backtraces = debug_backtrace();
        // 呼び出し元のクラスを取得
        $class = $backtraces[1]['class'];
        $objPage = $backtraces[1]['object'];
        $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($objPage->plugin_activate_flg);
        if (is_object($objPlugin)) {
            $objPlugin->doAction('SC_FormParam_construct', array($class, $this));
        }

        PowerPack::hook(get_called_class().'::__construct', array($this));
    }

    public function add($name, $type = 'text', $arrOption = array())
    {
        if ($arrOption['required']) {
            $arrOption['constraints'] = array('EXIST_CHECK') + $arrOption['constraints'];
        }
        $this->setOptions($name, $arrOption);

        if ($type == 'date') {
            $this->addDate(
                $this->getOption($name, 'label'),
                $name,
                $this->getOption($name, 'constraints', array()),
                $this->getOption($name, 'default', ''),
                $this->getOption($name, 'input_db', ture)
            );
        } elseif ($type == 'image') {
            $this->addImage(
                $this->getOption($name, 'label'),
                $name,
                $this->getOption($name, 'width'),
                $this->getOption($name, 'height'),
                $this->getOption($name, 'required', false),
                $this->getOption($name, 'scaleImage', array()),
                $this->getOption($name, 'fileSuffix', array())
            );
        } else {
            $this->addParam(
                $this->getOption($name, 'label'),
                $name,
                $this->getOption($name, 'max_length', ''),
                $this->getOption($name, 'convert', ''),
                $this->getOption($name, 'constraints', array()),
                $this->getOption($name, 'default', ''),
                $this->getOption($name, 'input_db', ture)
            );
        }
        $this->setType($name, $type);

        return $this;
    }

    public function setOptions($name, $arrOption)
    {
        $this->arrOptions[$name] = $arrOption;
    }

    public function getOption($name, $type, $default = null)
    {
        if (isset($this->arrOptions[$name][$type])) {
            return $this->arrOptions[$name][$type];
        } else {
            return $default;
        }
    }

    public function setType($name, $type)
    {
        $this->type[$name] = $type;
    }

    public function getType($name)
    {
        if (!empty($this->type[$name])) {
            return $this->type[$name];
        } else {
            return 'text';
        }
    }

    public function addParam($disp_name, $keyname, $length = '', $convert = '', $arrCheck = array(), $default = '', $input_db = true)
    {
        if (!in_array($keyname, $this->keyname)) {
            parent::addParam($disp_name, $keyname, $length, $convert, $arrCheck, $default, $input_db);
        }

        return $this;
    }

    public function addDate($disp_name, $keyname, $arrCheck = array(), $default = '', $input_db = true)
    {
        $this->addParam($disp_name, $keyname, STEXT_LEN, 'KVa', $arrCheck, $default, $input_db);

        $this->addParam($disp_name . '(年)', $keyname . '_year', 4, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '', false);
        $this->addParam($disp_name . '(月)', $keyname . '_month', 2, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '', false);
        $this->addParam($disp_name . '(日)', $keyname . '_day', 2, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '', false);

        $this->arrDate[$keyname] = $disp_name;

        return $this;
    }

    public function addImage($disp_name, $keyname, $width, $height, $necessary = false, $scaleImage = array(), $suffix = array())
    {
        if (array_search($this->image_key, $this->keyname) === false) {
            $this->addParam($this->image_key, $this->image_key, '', '', array(), '', false);

            // アップロードファイル情報の初期化
            $this->objUpFile = new SC_UploadFile_Ex(IMAGE_TEMP_REALDIR, IMAGE_SAVE_REALDIR);
        }

        $this->addImageColumn($keyname);
        $this->objUpFile->addFile($disp_name, $keyname, array('jpg', 'gif', 'png'), IMAGE_SIZE, $necessary, $width, $height);
        $this->arrScaleImage[$keyname] = $scaleImage;
        $this->arrSuffix[$keyname] = $suffix;

        return $this;
    }

    public function addImageColumn($keyname)
    {
        $this->addParam('save_'.$keyname, 'save_'.$keyname, '', '', array(), '', false);
        $this->addParam('temp_'.$keyname, 'temp_'.$keyname, '', '', array(), '', false);

        return $this;
    }

    /**
     * ファイルアップロード
     *
     * @return string
     */
    public function uploadImage()
    {
        $image_key = $this->getValue($this->image_key);

        // ファイルを一時ディレクトリにアップロード
        $err = $this->objUpFile->makeTempFile($image_key, IMAGE_RENAME);

        if ($err) {
            return $err;
        }

        // 縮小画像作成
        $this->setScaleImage($image_key);

        parent::setParam($this->objUpFile->getHiddenFileList());

        return '';
    }

    /**
     * アップロードファイルパラメーター情報から削除
     * 一時ディレクトリに保存されている実ファイルも削除する
     *
     * @return void
     */
    public function deleteImage()
    {
        $image_key = $this->getValue($this->image_key);

        // TODO: SC_UploadFile::deleteFileの画像削除条件見直し要
        $arrTempFile = $this->objUpFile->temp_file;
        $arrSaveFile = $this->objUpFile->save_file;
        $arrKeyName = $this->objUpFile->keyname;

        foreach ($arrKeyName as $key => $keyname) {
            if ($keyname != $image_key) {
                continue;
            }

            if (!empty($arrTempFile[$key])) {
                $temp_file = $arrTempFile[$key];
                $arrTempFile[$key] = '';

                if (!in_array($temp_file, $arrTempFile)) {
                    $this->objUpFile->deleteFile($image_key);
                } else {
                    $this->objUpFile->temp_file[$key] = '';
                    $this->objUpFile->save_file[$key] = '';
                }
            } else {
                $this->objUpFile->temp_file[$key] = '';
                $this->objUpFile->save_file[$key] = '';
            }
        }

        parent::setParam($this->objUpFile->getHiddenFileList());
    }

    /**
     * アップロードファイルを保存する
     *
     * @param  integer $value  主キーの値
     * @return void
     */
    public function saveImage($value = null)
    {
        // TODO: SC_UploadFile::moveTempFileの画像削除条件見直し要
        $objImage = new SC_Image_Ex($this->objUpFile->temp_dir);
        $arrKeyName = $this->objUpFile->keyname;
        $arrTempFile = $this->objUpFile->temp_file;
        $arrSaveFile = $this->objUpFile->save_file;
        $arrImageKey = array();
        foreach ($arrTempFile as $key => $temp_file) {
            if ($temp_file) {
                $objImage->moveTempImage($temp_file, $this->objUpFile->save_dir);
                $this->objUpFile->temp_file[$key] = '';
                $this->objUpFile->save_file[$key] = $temp_file;

                $arrImageKey[] = $arrKeyName[$key];
                $save_image_old = $arrSaveFile[$key]; // 過去にアップされて今回アップしたため削除したいキー
                if (!empty($save_image_old) && !in_array($temp_file, $arrSaveFile)) {
                    if ($value == null || !$this->checkSameImage($value, $arrImageKey, $save_image_old)) {
                        $objImage->deleteImage($save_image_old, $this->objUpFile->save_dir);
                    }
                }
            }
        }
    }

    /**
     * 縮小した画像をセットする
     *
     * @param  string $image_key 画像ファイルキー
     * @return void
     */
    public function setScaleImage($image_key)
    {
        if (isset($this->arrScaleImage[$image_key]) && $this->arrScaleImage[$image_key]) {
            foreach ((array) $this->arrScaleImage[$image_key] as $scale_image) {
                $this->makeScaleImage($image_key, $scale_image);
            }
        }
    }

    /**
     * 画像ファイルのコピー
     *
     * @return void
     */
    public function copyImages()
    {
        $arrKey = $this->objUpFile->keyname;
        $arrSaveFile = $this->objUpFile->save_file;

        foreach ($arrSaveFile as $key => $val) {
            $this->makeScaleImage($arrKey[$key], $arrKey[$key], true);
        }
    }

    /**
     * 縮小画像生成
     *
     * @param  string  $from_key  元画像ファイルキー
     * @param  string  $to_key    縮小画像ファイルキー
     * @param  boolean $forced
     * @return void
     */
    public function makeScaleImage($from_key, $to_key, $forced = false)
    {
        $arrImageKey = array_flip($this->objUpFile->keyname);
        $from_path = '';

        if ($this->objUpFile->temp_file[$arrImageKey[$from_key]]) {
            $from_path = $this->objUpFile->temp_dir . $this->objUpFile->temp_file[$arrImageKey[$from_key]];
        } elseif ($this->objUpFile->save_file[$arrImageKey[$from_key]]) {
            $from_path = $this->objUpFile->save_dir . $this->objUpFile->save_file[$arrImageKey[$from_key]];
        }

        if (file_exists($from_path)) {
            // 生成先の画像サイズを取得
            $to_w = $this->objUpFile->width[$arrImageKey[$to_key]];
            $to_h = $this->objUpFile->height[$arrImageKey[$to_key]];

            if ($forced) {
                $this->objUpFile->save_file[$arrImageKey[$to_key]] = '';
            }

            if (empty($this->objUpFile->temp_file[$arrImageKey[$to_key]]) && empty($this->objUpFile->save_file[$arrImageKey[$to_key]])) {
                // リネームする際は、自動生成される画像名に一意となるように、Suffixを付ける
                $dst_file = $this->objUpFile->lfGetTmpImageName(IMAGE_RENAME, '', $this->objUpFile->temp_file[$arrImageKey[$from_key]]) . $this->getAddSuffix($to_key);
                $path = $this->objUpFile->makeThumb($from_path, $to_w, $to_h, $dst_file);
                $this->objUpFile->temp_file[$arrImageKey[$to_key]] = basename($path);
            }
        }
    }

    /**
     * リネームする際は、自動生成される画像名に一意となるように、Suffixを付ける
     *
     * @param  string $to_key
     * @return string
     */
    public function getAddSuffix($to_key)
    {
        if (IMAGE_RENAME === true) {
            return;
        }

        // 自動生成される画像名
        if (!empty($this->arrSuffix[$to_key])) {
            return $this->arrSuffix[$to_key];
        } else {
            return '_copy';
        }
    }

    /**
     * 同名画像ファイル登録の有無を確認する.
     *
     * 画像ファイルの削除可否判定用。
     * 同名ファイルの登録がある場合には画像ファイルの削除を行わない。
     * 戻り値： 同名ファイル有り(true) 同名ファイル無し(false)
     *
     * @param  string  $primary_key     主キーのID
     * @param  string  $arrImageKey     対象としない画像カラム名
     * @param  string  $image_file_name 画像ファイル名
     * @return boolean
     */
    public function checkSameImage($primary_key, $arrImageKey, $image_file_name)
    {
        if (!SC_Utils_Ex::sfIsInt($primary_key)) {
            return false;
        }
        if (!$arrImageKey) {
            return false;
        }
        if (!$image_file_name) {
            return false;
        }
        if (!$this->primary_key) {
            return false;
        }
        if (!$this->table_name) {
            return false;
        }

        $arrWhere = array();
        $sqlval = array('0', $primary_key);
        foreach ($arrImageKey as $image_key) {
            $arrWhere[] = "{$image_key} = ?";
            $sqlval[] = $image_file_name;
        }
        $where = implode(' OR ', $arrWhere);
        $where = "del_flg = ? AND (({$this->primary_key} <> ? AND ({$where}))";

        $arrKeyName = $this->objUpFile->keyname;
        foreach ($arrKeyName as $key => $keyname) {
            if (in_array($keyname, $arrImageKey))
                continue;
            $where .= " OR {$keyname} = ?";
            $sqlval[] = $image_file_name;
        }
        $where .= ')';

        $objQuery = & SC_Query_Ex::getSingletonInstance();
        $exists = $objQuery->exists($this->table_name, $where, $sqlval);

        return $exists;
    }

    /**
     * getFormImageList
     *
     * @param type $temp_url
     * @param type $save_url
     * @return type
     */
    public function getFormImageList($temp_url = IMAGE_TEMP_URLPATH, $save_url = IMAGE_SAVE_URLPATH)
    {
        return $this->objUpFile->getFormFileList($temp_url, $save_url);
    }


    /* 標準項目 */
    /**
     * initParam
     */
    public function initParam()
    {
        parent::initParam();
    }

    public function bind($arrVal)
    {
        $this->setParam($arrVal);
        $this->convParam();
    }

    /**
     * $_POST などをセットする
     *
     * @param array $arrVal $arrVal['keyname']・・の配列を一致したキーのインスタンスに格納する
     * @param bool $seq trueの場合、$arrVal[0]~の配列を登録順にインスタンスに格納する
     */
    public function setParam($arrVal, $seq = false)
    {
        if ($seq === false) {
            foreach ($this->arrDate as $date_key => $date_name) {
                if (!SC_Utils_Ex::isBlank($arrVal[$date_key])) {
                    $ts = strtotime($arrVal[$date_key]);
                    $arrVal[$date_key . '_year'] = date('Y', $ts);
                    $arrVal[$date_key . '_month'] = date('n', $ts);
                    $arrVal[$date_key . '_day'] = date('j', $ts);
                } elseif (
                    !SC_Utils_Ex::isBlank($arrVal[$date_key . '_year'])
                    && !SC_Utils_Ex::isBlank($arrVal[$date_key . '_month'])
                    && !SC_Utils_Ex::isBlank($arrVal[$date_key . '_day'])
                ) {
                    $arrVal[$date_key] = SC_Utils_Ex::sfGetTimestamp(
                        $arrVal[$date_key . '_year'], $arrVal[$date_key . '_month'], $arrVal[$date_key . '_day']
                    );
                }
            }
        }

        parent::setParam($arrVal, $seq);

        if ($this->objUpFile) {
            $this->objUpFile->setHiddenFileList($arrVal);
        }
    }

    /**
     * DB のデータをセットする
     *
     * @param array $arrVal $arrVal['keyname']・・の配列を一致したキーのインスタンスに格納する
     */
    public function setData($arrVal)
    {
        if ($this->objUpFile) {
            $arrKeyName = $this->objUpFile->keyname;
            foreach ($arrKeyName as $key => $keyname) {
                if (array_key_exists($keyname, $arrVal)) {
                    $arrVal['save_' . $keyname] = $arrVal[$keyname];
                }
            }
        }

        foreach ($this->arrDate as $date_key => $date_name) {
            if (!SC_Utils_Ex::isBlank($arrVal[$date_key])) {
                $ts = strtotime($arrVal[$date_key]);
                $arrVal[$date_key . '_year'] = date('Y', $ts);
                $arrVal[$date_key . '_month'] = date('n', $ts);
                $arrVal[$date_key . '_day'] = date('j', $ts);
            }
        }

        parent::setParam($arrVal);

        if ($this->objUpFile) {
            $this->objUpFile->setDBFileList($arrVal);
        }
    }

    /**
     * 連想配列で返す
     *
     * @param  array $arrKey 対象のキー
     * @return array 連想配列
     */
    public function getHashArray($arrKey = array())
    {
        $arrRet = parent::getHashArray($arrKey);

        // 画像ファイル表示用データ取得
        if (empty($arrKey) && $this->objUpFile) {
            $dbFileList = $this->objUpFile->getDBFileList();
            $arrRet = array_merge($arrRet, $dbFileList);
        }

        return $arrRet;
    }

    /**
     * DB格納用配列の作成
     *
     * @return array 連想配列
     */
    public function getDbArray()
    {
        $dbArray = parent::getDbArray();

        // 画像ファイル表示用データ取得
        if ($this->objUpFile) {
            $dbFileList = $this->objUpFile->getDBFileList();
            $dbArray = array_merge($dbArray, $dbFileList);
        }

        return $dbArray;
    }

    /**
     * checkError
     *
     * @param bool $br
     * @return array
     */
    public function checkError($br = true)
    {
        PowerPack::hook('SC_FormParam::checkError', array($this, $br));

        $arrErr = parent::checkError($br);

        PowerPack::hook(get_called_class().'::checkError', array($this, $br, &$arrErr));

        // アップロードファイル必須チェック
        if ($this->objUpFile) {
            $arrErr = array_merge((array) $arrErr, (array) $this->objUpFile->checkExists());
        }

        // 日付エラー
        $objError = new SC_CheckError_Ex($this->getHashArray());
        foreach ($this->arrDate as $date_key => $date_name) {
            $objError->doFunc(array($date_name, $date_key . '_year', $date_key . '_month', $date_key . '_day'), array('CHECK_DATE'));
            $err = $objError->arrErr[$date_key . '_year'];
            if ($err || $arrErr[$date_key . '_year'] || $arrErr[$date_key . '_month'] || $arrErr[$date_key . '_day']) {
                $arrErr[$date_key] .= $err . $arrErr[$date_key . '_year'] . $arrErr[$date_key . '_month'] . $arrErr[$date_key . '_day'];
            }
        }

        $this->arrErr = $arrErr;

        return $arrErr;
    }

    public function isValid()
    {
        $this->checkError();
        $this->validate = true;

        if (empty($this->arrErr)) {
            return ture;
        } else {
            return false;
        }
    }

    public function getError($name)
    {
        return !empty($this->arrErr[$name]) ? $this->arrErr[$name] : null;
    }

    public function getErrors()
    {
        return $this->arrErr;
    }

    // フォームに渡す用のパラメーターを返す
    public function createView()
    {
        $arrView = array();
        foreach ($this->keyname as $index => $key) {
            $arrView[$key] = array(
                'type'      => $this->getType($key),
                'name'      => $key,
                'label'     => $this->disp_name[$index],
                'max_length' => $this->length[$index],
                'value'     => $this->getValue($key),
                'required'  => in_array('EXIST_CHECK', $this->arrCheck[$index]) ? true : false,
                'error'     => $this->getError($key),
                'attr'      => $this->getOption($key, 'attr', array()),
                'help'      => $this->getOption($key, 'help', ''),
                'prefix'    => $this->getOption($key, 'prefix', ''),
                'suffix'    => $this->getOption($key, 'suffix', ''),
            );
            if ($arrView[$key]['max_length'] && empty($arrView[$key]['attr']['maxlength'])) {
                $arrView[$key]['attr']['maxlength'] = $arrView[$key]['max_length'];
            }
            if ($arrView[$key]['type'] == 'image') {
                $arrView[$key]['width'] = $this->getOption($key, 'width');
                $arrView[$key]['height'] = $this->getOption($key, 'height');
            } elseif (in_array($arrView[$key]['type'], array('choice', 'select', 'radio', 'checkbox', 'date'))) {
                $arrView[$key]['choices'] = $this->getOption($key, 'choices', array());
                $arrView[$key]['empty_data'] = $this->getOption($key, 'empty_data', array());
            }
        }

        return $arrView;
    }


    /* ArrayAccess */
    /**
     * offsetExists
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        if ($this->objUpFile) {
            return (array_search($offset, $this->keyname) !== false || array_search($offset, $this->objUpFile->keyname) !== false);
        } else {
            return array_search($offset, $this->keyname) !== false;
        }
    }

    /**
     * offsetGet
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getValue($offset);
    }

    /**
     * offsetSet
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->setValue($offset, $value);
    }

    /**
     * offsetUnset
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->removeParam($offset);
    }

}
