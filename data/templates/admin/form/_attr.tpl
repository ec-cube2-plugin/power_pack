<!--{strip}-->
    <!--{foreach from=$attr key=key item=items}-->
        <!--{" "}--><!--{$key}-->="<!--{if is_array($items)}--><!--{' '|implode:$items}--><!--{else}--><!--{$items}--><!--{/if}-->"
    <!--{/foreach}-->
<!--{/strip}-->