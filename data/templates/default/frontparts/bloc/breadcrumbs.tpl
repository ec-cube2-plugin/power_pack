<!--{if $smarty.server.SCRIPT_NAME != $smarty.const.ROOT_URLPATH|cat:"index.php"}-->
    <ol id="breadcrumbs">
        <li class="root"><a href="<!--{$smarty.const.ROOT_URLPATH}-->">TOP</a></li>
        <!--{foreach from=$arrPaths item=arrPath name=cnt}-->
            <!--{if $arrPath.path}-->
                <li><a href="<!--{$arrPath.path}-->"><!--{$arrPath.name}--></a></li>
            <!--{else}-->
                <li><!--{$arrPath.name}--></li>
            <!--{/if}-->
        <!--{/foreach}-->
    </ol>
<!--{/if}-->
