<?php

/**
 * Class SC_PluginProvider_Path
 *
 * パンくずリスト
 */
class SC_PluginProvider_Path extends SC_PluginProvider_Base
{
    public function register(SC_Plugin_Base $plugin, SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        $objHelperPlugin->addAction('LC_Page_process', array($this, 'LC_Page_process'), $priority);
        $objHelperPlugin->addAction("LC_Page_Products_List_action_after", array($this, "LC_Page_Products_List_action_after"), $priority);
    }

    public function LC_Page_process(LC_Page $objPage)
    {
        // path
        $objPage->arrPaths = array();
        if ($objPage instanceof LC_Page_Products_Detail) {
            $arrRelativeCats = reset($objPage->arrRelativeCat);
            foreach ($arrRelativeCats as $arrRelativeCat) {
                $objPage->arrPaths[] = array(
                    'name' => $arrRelativeCat['category_name'],
                    'path' => ROOT_URLPATH . 'products/list.php?category_id=' . $arrRelativeCat['category_id'],
                );
            }
            $objPage->arrPaths[] = array(
                'name' => $objPage->arrProduct['name'],
                'path' => null,
            );
        } elseif ($objPage instanceof LC_Page_Products_List) {
            foreach ($objPage->arrParentCategories as $arrParentCategory) {
                $objPage->arrPaths[] = array(
                    'name' => $arrParentCategory['category_name'],
                    'path' => ROOT_URLPATH . 'products/list.php?category_id=' . $arrParentCategory['category_id'],
                );
            }
            $name = '';
            if ($objPage->arrCategory) {
                $name .= $objPage->arrCategory['category_name'] . ' ';
            }
            if ($objPage->arrMaker) {
                $name .= $objPage->arrMaker['name'] . ' ';
            }
            if ($objPage->arrProductStatus) {
                $name .= $objPage->arrProductStatus['name'] . ' ';
            }
            if (empty($name)) {
                $name .= '全商品 ';
            }
            if ($objPage->arrSearchData['name']) {
                $name .= 'から ' . $objPage->arrSearchData['name'] . ' で検索';
            }
            $objPage->arrPaths[] = array(
                'name' => $name,
                'path' => null,
            );
        } else {
            if ($objPage->tpl_title) {
                $uri = $_SERVER['REQUEST_URI'];
                if ($objPage->tpl_subtitle) {
                    $arrPath = explode("/", $uri);
                    $objPage->arrPaths[] = array(
                        'name' => $objPage->tpl_title,
                        'path' => count($arrPath) == 3 ? ROOT_URLPATH . $arrPath[1] . '/' : null,
                    );
                    $objPage->arrPaths[] = array(
                        'name' => $objPage->tpl_subtitle,
                        'path' => null,
                    );
                } else {
                    $arrTitle = explode("/", $objPage->tpl_title);
                    $offset = strpos($uri, ROOT_URLPATH);
                    if ($offset === 0) {
                        $uri = substr($uri, strlen(ROOT_URLPATH));
                    }
                    $arrPath = explode("/", $uri);
                    $path = ROOT_URLPATH;
                    if ($arrPath[0] == 'user_data' && count($arrTitle) + 1 == count($arrPath)) {
                        $path .= $arrPath[0] . '/';
                        for ($i = 0; $i < count($arrTitle) - 1; $i++) {
                            $path .= $arrPath[$i + 1] . '/';
                            $objPage->arrPaths[] = array(
                                'name' => $arrTitle[$i],
                                'path' => $path,
                            );
                        }
                        $objPage->arrPaths[] = array(
                            'name' => $arrTitle[$i],
                            'path' => null,
                        );
                    } elseif ($arrPath[0] != 'user_data' && count($arrTitle) == count($arrPath)) {
                        for ($i = 0; $i < count($arrTitle) - 1; $i++) {
                            $path .= $arrPath[$i] . '/';
                            $objPage->arrPaths[] = array(
                                'name' => $arrTitle[$i],
                                'path' => $path,
                            );
                        }
                        $objPage->arrPaths[] = array(
                            'name' => $arrTitle[$i],
                            'path' => null,
                        );
                    } else {
                        for ($i = 0; $i < count($arrTitle); $i++) {
                            $objPage->arrPaths[] = array(
                                'name' => $arrTitle[$i],
                                'path' => null,
                            );
                        }
                    }
                }
            }
        }
        if (count($objPage->arrPaths) >= 1) {
            $objPage->page_title = $objPage->arrPaths[count($objPage->arrPaths) - 1]['name'];
        } else {
            $objPage->page_title = '';
        }
    }

    public function LC_Page_Products_List_action_after(LC_Page_Products_List $objPage)
    {
        $objPage->arrParentCategories = array();
        if ($objPage->arrSearchData['category_id']) {
            // パンくず用
            $objQuery = SC_Query_Ex::getSingletonInstance();
            $objPage->arrCategory = $objQuery->getRow('dtb_category.*', 'dtb_category', 'dtb_category.category_id = ?', $objPage->arrSearchData['category_id']);
            $parent_category = $objPage->arrCategory;
            while ($parent_category['parent_category_id']) {
                $objQuery = SC_Query_Ex::getSingletonInstance();
                $parent_category = $objQuery->getRow('dtb_category.*', 'dtb_category', 'dtb_category.category_id = ?', $parent_category['parent_category_id']);
                if ($parent_category) {
                    array_unshift($objPage->arrParentCategories, $parent_category);
                } else {
                    break;
                }
            }
        }
    }
}
