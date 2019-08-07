<?php

/**
 * Class SC_PluginProvider_CustomColumn
 *
 * カラムカスタマイズ
 */
class SC_PluginProvider_CustomColumn extends SC_PluginProvider_Base
{
    public function install($arrPlugin, SC_Plugin_Installer $objPluginInstaller = null)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();

        // plg_PowerPack_dtb_column
        $sql = <<<__EOF__
            CREATE TABLE plg_PowerPack_dtb_column (
                id INTEGER,
                target VARCHAR(255) NOT NULL,
                name VARCHAR(255) NOT NULL,
                col VARCHAR(255) NOT NULL,
                type VARCHAR(255) NOT NULL,
                length INTEGER,
                width INTEGER,
                height INTEGER,
                prefix TEXT,
                suffix TEXT,
                note TEXT,
                convert_option VARCHAR(255),
                error_check TEXT,
                searchable SMALLINT DEFAULT 0 NOT NULL,
                rank INTEGER NOT NULL DEFAULT 0,
                PRIMARY KEY (id)
            );
__EOF__;
        $objQuery->query($sql);
    }

    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        $this->initColumn();

        // LC_Page_Products_List
        PowerPack::addFormParam('LC_Page_Products_List', function (plg_PowerPack_SC_FormParam $objFormParam) use ($priority) {
            foreach (PowerPack::$arrColumns['product'] as $arrColumn) {
                if ($arrColumn['searchable']) {
                    $objFormParam->addParam($arrColumn['name'], $arrColumn['col'], $arrColumn['length'], $arrColumn['convert_option']);
                }
            }

            $count = 0;
            PowerPack::addAction('SC_Product::findProductCount', function (plg_PowerPack_SC_Product $objProduct, SC_Query $objQuery, &$arrVal) use ($objFormParam, &$count, $priority) {
                $count = count($arrVal);
                foreach (PowerPack::$arrColumns['product'] as $arrColumn) {
                    if ($arrColumn['searchable'] && $objFormParam->getValue($arrColumn['col'])) {
                        if ($arrColumn['searchable'] == 1) {
                            $objQuery->where .= ' AND alldtl.' . $arrColumn['col'] . ' = ? ';
                            $arrVal[] = $objFormParam->getValue($arrColumn['col']);
                        }
                    }
                }
            }, $priority);
            PowerPack::addAction('SC_Product::findProductIdsOrder', function (plg_PowerPack_SC_Product $objProduct, SC_Query $objQuery, &$arrVal) use ($objFormParam, &$count, $priority) {
                if ($count > 0) {
                    $arrVal2 = array_slice($arrVal, 0, $count);
                } else {
                    $arrVal2 = $arrVal;
                }
                foreach (PowerPack::$arrColumns['product'] as $arrColumn) {
                    if ($arrColumn['searchable'] && $objFormParam->getValue($arrColumn['col'])) {
                        if ($arrColumn['searchable'] == 1) {
                            $objQuery->where .= ' AND alldtl.' . $arrColumn['col'] . ' = ? ';
                            $arrVal2[] = $objFormParam->getValue($arrColumn['col']);
                        }
                    }
                }
                if ($count > 0) {
                    $arrVal2 = array_merge($arrVal2, array_slice($arrVal, $count));
                }
                $arrVal = $arrVal2;
            }, $priority);
        }, $priority);
        $objHelperPlugin->addAction("LC_Page_Products_List_action_before", array($this, "LC_Page_Products_List_action_before"), $priority);

        // LC_Page_Products_Detail
        $objHelperPlugin->addAction("LC_Page_Products_Detail_action_before", array($this, "LC_Page_Products_Detail_action_before"), $priority);
        $objHelperPlugin->addAction("LC_Page_Products_Detail_action_after", array($this, "LC_Page_Products_Detail_action_after"), $priority);

        // LC_Page_Admin_Products
        PowerPack::addPrefilter(DEVICE_TYPE_ADMIN, 'products/index.tpl', function(&$source) {
            $objTransform = new SC_Helper_Transform($source);
            $objTransform->select('#search_form table', 0)->appendChild('<!--{include file=\'products/_index_search.tpl\'}-->');
            $source = $objTransform->getHTML();
        });
        PowerPack::addFormParam('LC_Page_Admin_Products', function (plg_PowerPack_SC_FormParam $objFormParam) use ($priority) {
            foreach (PowerPack::$arrColumns['product'] as $arrColumn) {
                if ($arrColumn['searchable']) {
                    $objFormParam->addParam($arrColumn['name'], 'search_' . $arrColumn['col'], $arrColumn['length'], $arrColumn['convert_option']);
                }
            }
            PowerPack::addAction('LC_Page_Admin_Products::buildQuery', function (plg_PowerPack_LC_Page_Admin_Products $objPage, $key, &$where, &$arrValues, $objFormParam, $objDb) {
                $key2 = preg_replace('/\Asearch_/', '', $key);
                if (array_key_exists($key2, PowerPack::$arrColumns['product'])) {
                    $arrColumn = PowerPack::$arrColumns['product'][$key2];
                    if ($arrColumn['searchable'] && $objFormParam->getValue($key)) {
                        if ($arrColumn['searchable'] == 1) {
                            $where .= ' AND ' . $arrColumn['col'] . ' = ?';
                            $arrValues[] = $objFormParam->getValue($key);
                        } elseif ($arrColumn['searchable'] == 2) {
                            $where .= ' AND ' . $arrColumn['col'] . ' LIKE ?';
                            $arrValues[] = '%' . $objFormParam->getValue($key) . '%';
                        }
                    }
                }
            }, $priority);
        }, $priority);
        PowerPack::addAction('LC_Page_Admin_Products::init', function (plg_PowerPack_LC_Page_Admin_Products $objPage) {
            $objPage->arrColumns = PowerPack::$arrColumns['product'];
            $masterData = new SC_DB_MasterData_Ex();
            foreach (PowerPack::$arrColumns['product'] as $arrColumn) {
                if ($arrColumn['type'] == 'select') {
                    $objPage->arrSelect[$arrColumn['col']] = $masterData->getMasterData('plg_PowerPack_mtb_' . $arrColumn['col']);
                }
            }
        });

        // LC_Page_Admin_Products_Product
        PowerPack::addPrefilter(DEVICE_TYPE_ADMIN, 'products/product.tpl', function(&$source) {
            $objTransform = new SC_Helper_Transform($source);
            $objTransform->select('#products table.form', 0)->appendChild('<!--{include file=\'products/_product_column.tpl\'}-->');
            $source = $objTransform->getHTML();
        });
        PowerPack::addPrefilter(DEVICE_TYPE_ADMIN, 'products/confirm.tpl', function(&$source) {
            $source = str_replace('<!--{* オペビルダー用 *}-->', '<!--{include file=\'products/_confirm_column.tpl\'}--><!--{* オペビルダー用 *}-->', $source);
        });
        PowerPack::addFormParam('LC_Page_Admin_Products_Product', function (plg_PowerPack_SC_FormParam $objFormParam) use ($priority) {
            foreach (PowerPack::$arrColumns['product'] as $arrColumn) {
                if ($arrColumn['searchable']) {
                    $objFormParam->addParam($arrColumn['name'], 'search_' . $arrColumn['col'], $arrColumn['length'], $arrColumn['convert_option']);
                }
            }

            if ($_REQUEST['mode'] != 'pre_edit' && $_REQUEST['mode'] != 'copy') {
                foreach (PowerPack::$arrColumns['product'] as $arrColumn) {
                    if ($arrColumn['type'] === 'date') {
                        $objFormParam->addDate($arrColumn['name'], $arrColumn['col'], $arrColumn['arrCheck']);
                    } elseif ($arrColumn['type'] === 'image') {
                        $objFormParam->addImageColumn($arrColumn['col']);
                    } else {
                        $objFormParam->addParam($arrColumn['name'], $arrColumn['col'], $arrColumn['length'], $arrColumn['convert'], $arrColumn['arrCheck']);
                    }
                }
            }

            PowerPack::addAction('SC_UploadFile::getDBFileList', function (plg_PowerPack_SC_UploadFile $objUploadFile, &$dbFileList) use ($objFormParam, $priority, $arrRanks) {
                if ($objUploadFile->temp_dir === IMAGE_TEMP_REALDIR && $objUploadFile->save_dir === IMAGE_SAVE_REALDIR && $objUploadFile->class == 'LC_Page_Admin_Products_Product') {
                    $arrForm = $objFormParam->getHashArray();
                    foreach (PowerPack::$arrColumns['product'] as $arrColumn) {
                        if ($arrColumn['type'] != 'image') {
                            $dbFileList[$arrColumn['col']] = $arrForm[$arrColumn['col']];
                        }
                    }
                }
            }, $priority);
        }, $priority);
        PowerPack::addUploadFile('LC_Page_Admin_Products_Product', function (plg_PowerPack_SC_UploadFile $objUploadFile) {
            if ($objUploadFile->temp_dir === IMAGE_TEMP_REALDIR && $objUploadFile->save_dir === IMAGE_SAVE_REALDIR) {
                foreach (PowerPack::$arrColumns['product'] as $arrColumn) {
                    if ($arrColumn['type'] === 'image') {
                        $objUploadFile->addFile($arrColumn['name'], $arrColumn['col'], array('jpg', 'gif', 'png'), IMAGE_SIZE, $arrColumn['required'], $arrColumn['width'], $arrColumn['height']);
                    }
                }
            }
        }, $priority);
        $objHelperPlugin->addAction("LC_Page_Admin_Products_Product_action_before", array($this, "LC_Page_Admin_Products_Product_action_before"), $priority);
        $objHelperPlugin->addAction("LC_Page_Admin_Products_Product_action_after", array($this, "LC_Page_Admin_Products_Product_action_after"), $priority);

        // LC_Page_Admin_Products_ProductClass
        PowerPack::addFormParam('LC_Page_Admin_Products_ProductClass', function (plg_PowerPack_SC_FormParam $objFormParam) use ($priority) {
            foreach (PowerPack::$arrColumns['product'] as $arrColumn) {
                if ($arrColumn['searchable']) {
                    $objFormParam->addParam($arrColumn['name'], 'search_' . $arrColumn['col'], $arrColumn['length'], $arrColumn['convert_option']);
                }
            }
        }, $priority);
    }

    public function initColumn()
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->setOrder('rank ASC');
        $arrColumns = $objQuery->select('*', 'plg_PowerPack_dtb_column');
        foreach ($arrColumns as $arrColumn) {
            if ($arrColumn['error_check']) {
                $arrColumn['arrCheck'] = explode(',', $arrColumn['error_check']);
            } else {
                $arrColumn['arrCheck'] = array();
            }
            if ($arrColumn['length']) {
                $arrColumn['arrCheck'] += array('MAX_LENGTH_CHECK');
            }
            if (in_array('EXIST_CHECK', $arrColumn['arrCheck'])) {
                $arrColumn['required'] = true;
            } else {
                $arrColumn['required'] = false;
            }
            PowerPack::$arrColumns[$arrColumn['target']][$arrColumn['col']] = $arrColumn;
        }
    }

    public function LC_Page_Products_List_action_before(LC_Page_Products_List $objPage)
    {
        $masterData = new SC_DB_MasterData_Ex();
        foreach (PowerPack::$arrColumns['product'] as $arrColumn) {
            if ($arrColumn['type'] == 'select') {
                $objPage->arrSelect[$arrColumn['col']] = $masterData->getMasterData('plg_PowerPack_mtb_' . $arrColumn['col']);
            }
        }
    }

    public function LC_Page_Products_Detail_action_before(LC_Page_Products_Detail $objPage)
    {
        $masterData = new SC_DB_MasterData_Ex();
        foreach (PowerPack::$arrColumns['product'] as $arrColumn) {
            if ($arrColumn['type'] == 'select') {
                $objPage->arrSelect[$arrColumn['col']] = $masterData->getMasterData('plg_PowerPack_mtb_' . $arrColumn['col']);
            }
        }
    }

    public function LC_Page_Products_Detail_action_after(LC_Page_Products_Detail $objPage)
    {
        foreach (PowerPack::$arrColumns['product'] as $arrColumn) {
            if ($arrColumn['type'] == 'image') {
                $objPage->objUpFile->addFile($arrColumn['name'], $arrColumn['col'], array('jpg'), IMAGE_SIZE);
            }
        }
        $objPage->objUpFile->setDBFileList($objPage->arrProduct);
        $objPage->arrFile = $objPage->objUpFile->getFormFileList(IMAGE_TEMP_URLPATH, IMAGE_SAVE_URLPATH, true);
    }

    public function LC_Page_Admin_Products_Product_action_before(LC_Page_Admin_Products_Product $objPage)
    {
        // カラム追加
        $objPage->arrColumns = PowerPack::$arrColumns['product'];
        $masterData = new SC_DB_MasterData_Ex();
        foreach (PowerPack::$arrColumns['product'] as $arrColumn) {
            if ($arrColumn['type'] == 'select') {
                $objPage->arrSelect[$arrColumn['col']] = $masterData->getMasterData('plg_PowerPack_mtb_' . $arrColumn['col']);
            } elseif ($arrColumn['type'] == 'date') {
                $objDate = new SC_Date_Ex();
                $objDate->setStartYear(RELEASE_YEAR);
                $objDate->setEndYear(date('Y') + 10);
                $objPage->arrSelect[$arrColumn['col']] = array(
                    'year'  => $objDate->getYear(),
                    'month' => $objDate->getMonth(),
                    'day'   => $objDate->getDay(),
                );
            }
        }
    }

    public function LC_Page_Admin_Products_Product_action_after(LC_Page_Admin_Products_Product $objPage)
    {
        if ($objPage->getMode() == 'pre_edit' || $objPage->getMode() == 'copy') {
            // カラム
            foreach (PowerPack::$arrColumns['product'] as $arrColumn) {
                if ($arrColumn['type'] == 'date') {
                    if ($objPage->arrForm[$arrColumn['col']]) {
                        $time = strtotime($objPage->arrForm[$arrColumn['col']]);
                        $objPage->arrForm[$arrColumn['col'] . '_year'] = date('Y', $time);
                        $objPage->arrForm[$arrColumn['col'] . '_month'] = date('m', $time);
                        $objPage->arrForm[$arrColumn['col'] . '_day'] = date('d', $time);
                    }
                }
            }
        }
    }
}
