<!--▼CONTENTS-->
<div id="undercolumn">
    <div id="undercolumn_news">
        <h2 class="title">新着情報</h2>
        <dl>
            <dt><span class="date"><!--{$arrNews.cast_news_date|sfDispDBDate:false|h}--></span></dt>
            <dd>
                <!--{if $arrNews.news_url}-->
                    <a href="<!--{$arrNews.news_url}-->" <!--{if $arrNews.link_method eq "2"}--> target="_blank"<!--{/if}-->><!--{$arrNews.news_title|h|nl2br}--></a>
                <!--{else}-->
                    <!--{$arrNews.news_title|h|nl2br}-->
                <!--{/if}-->
            </dd>
        </dl>
        <p><!--{$arrNews.news_comment|h|nl2br}--></p>
    </div>
</div>
<!--▲CONTENTS-->
