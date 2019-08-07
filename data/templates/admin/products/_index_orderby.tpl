 表示順序
<!--{assign var=key value='search_orderby'}-->
<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
    <!--{html_options options=$arrOrderby selected=$arrForm[$key].value}-->
</select>
