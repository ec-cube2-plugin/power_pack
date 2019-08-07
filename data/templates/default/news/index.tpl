<!--▼CONTENTS-->
<div id="undercolumn">
    <div id="undercolumn_news">
        <h2 class="title">新着情報</h2>
        <dl>
        <!--{foreach from=$arrNewses item="arrNews" name="cnt"}-->
            <dt><span class="date"><!--{$arrNews.cast_news_date|sfDispDBDate:false|h}--></span></dt>
            <dd>
                <!--{if $arrNews.news_url}-->
                        <a href="<!--{$arrNews.news_url}-->" <!--{if $arrNews.link_method eq "2"}--> target="_blank"<!--{/if}-->><!--{$arrNews.news_title|h|nl2br}--></a>
                <!--{elseif $arrNews.news_comment}-->
                    <a href="<!--{$smarty.const.ROOT_URLPATH}-->news/detail.php?news_id=<!--{$arrNews.news_id|h}-->"><!--{$arrNews.news_title|h|nl2br}--></a>
                <!--{else}-->
                    <!--{$arrNews.news_title|h|nl2br}-->
                <!--{/if}-->
            </dd>
        <!--{/foreach}-->
        </dl>
    </div>
</div>
<!--▲CONTENTS-->
