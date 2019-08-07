<?php

/* ※使用条件※
    ・formタグに以下を追加する。
        <input type='hidden' name='pageno' value="<!--{$tpl_pageno}-->">
    ・ソースの最初に以下を記述する。
        $objPage->tpl_pageno = $_POST['pageno'];
    ・$func_nameに指定するJavaScriptの例
        // ページナビで使用する。
        eccube.movePage = function(pageno, mode, form) {
            if (typeof form !== 'undefined') {
                form = 'form1';
            }
            document.forms[form]['pageno'].value = pageno;
            if (typeof mode !== 'undefined') {
                document.forms[form]['mode'].value = 'search';
            }
            document.forms[form].submit();
        };
*/
class plg_PowerPack_SC_PageNavi extends SC_PageNavi
{

    // コンストラクタ
    public function __construct($now_page, $all_row, $page_row, $func_name, $navi_max = NAVI_PMAX, $urlParam = '', $display_number = true)
    {
        parent::__construct($now_page, $all_row, $page_row, $func_name, $navi_max, $urlParam, $display_number);

        // page
        $this->from = $this->start_row + 1;
        if ($this->start_row + $page_row < $all_row) {
            $this->to = $this->start_row + $page_row;
        } else {
            $this->to = $all_row;
        }

        if ($all_row > 1) {
            //「前へ」「次へ」の設定
            $before = '';
            $next = '';
            if ($this->now_page > 1) {
                $this->arrPagenavi['before'] = $this->now_page - 1;
                $urlParamThis = str_replace('#page#', $this->arrPagenavi['before'], $urlParam);
                $urlParamThis = htmlentities($urlParamThis, ENT_QUOTES);
                if ($func_name) {
                    $before = "<a href=\"?$urlParamThis\" onclick=\"$func_name('{$this->arrPagenavi['before']}'); return false;\">&lt;&lt;前へ</a> ";
                } else {
                    $before = "<a href=\"?$urlParamThis\">&lt;&lt;前へ</a> ";
                }
            } else {
                $this->arrPagenavi['before'] = $this->now_page;
            }

            if ($this->now_page < $this->max_page) {
                $this->arrPagenavi['next'] = $this->now_page + 1;
                $urlParamThis = str_replace('#page#', $this->arrPagenavi['next'], $urlParam);
                $urlParamThis = htmlentities($urlParamThis, ENT_QUOTES);
                if ($func_name) {
                    $next = " <a href=\"?$urlParamThis\" onclick=\"$func_name('{$this->arrPagenavi['next']}'); return false;\">次へ&gt;&gt;</a>";
                } else {
                    $next = " <a href=\"?$urlParamThis\">次へ&gt;&gt;</a>";
                }
            } else {
                $this->arrPagenavi['next'] = $this->now_page;
            }

            // 表示する最大ナビ数を決める。
            if ($navi_max == '' || $navi_max > $this->max_page) {
                // 制限ナビ数の指定がない。ページ最大数が制限ナビ数より少ない。
                $disp_max = $this->max_page;
            } else {
                // 現在のページ＋制限ナビ数が表示される。
                $disp_max = $this->now_page + $navi_max - 1;
                // ページ最大数を超えている場合は、ページ最大数に合わせる。
                if ($disp_max > $this->max_page) {
                    $disp_max = $this->max_page;
                }
            }

            // 表示する最小ナビ数を決める。
            if ($navi_max == '' || $navi_max > $this->now_page) {
                // 制限ナビ数の指定がない。現在ページ番号が制限ナビ数より少ない。
                $disp_min = 1;
            } else {
                // 現在のページ-制限ナビ数が表示される。
                $disp_min = $this->now_page - $navi_max + 1;
            }

            $this->arrPagenavi['arrPageno'] = array();
            $page_number = '';
            for ($i = $disp_min; $i <= $disp_max; $i++) {
                if ($i == $this->now_page) {
                    $page_number .= "<strong>$i</strong>";
                } else {
                    $urlParamThis = str_replace('#page#', $i, $urlParam);
                    $urlParamThis = htmlentities($urlParamThis, ENT_QUOTES);
                    if ($func_name) {
                        $page_number .= "<a href=\"?$urlParamThis\" onclick=\"$func_name('$i'); return false;\">$i</a>";
                    } else {
                        $page_number .= "<a href=\"?$urlParamThis\">$i</a>";
                    }
                }

                $page_number .= ' ';

                $this->arrPagenavi['arrPageno'][$i] = $i;
            }

            if ($before && $next) {
                $this->strnavi = $before .(($display_number) ? $page_number : ' | ') .$next;
            } elseif ($before || $next) {
                $this->strnavi = $before .(($display_number) ? $page_number : '') .$next;
            }
        }
    }
}
