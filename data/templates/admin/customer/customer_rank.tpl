<div id="customer" class="contents-main">
    <form name="search_form" id="search_form" method="post" action="?" >
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="search" />
        <input type="hidden" name="id" value="" />
    </form>

    <div class="btn">
        <ul>
            <li><a class="btn-action" href="./customer_rank_edit.php"><span class="btn-next">新規</span></a></li>
        </ul>
    </div>

    <table id="customer-rank-result" class="list">
        <colgroup>
            <col width="10%" />
            <col width="50%" />
            <col width="20%" />
            <col width="10%" />
            <col width="10%" />
        </colgroup>
        <tr>
            <th>ID</th>
            <th>名称</th>
            <th>会員数</th>
            <th class="edit">編集</th>
            <th class="delete">削除</th>
        </tr>
        <!--{foreach from=$arrCustomerRanks item=arrCustomerRank}-->
            <tr>
                <td class="center"><!--{$arrCustomerRank.id|h}--></td>
                <td><!--{$arrCustomerRank.name|h}--></td>
                <td class="right"><!--{$arrCustomerRank.count|h}--></td>
                <td class="menu"><a href="./customer_rank_edit.php?id=<!--{$arrCustomerRank.id}-->">編集</a></td>
                <td class="menu">
                    <!--{if $arrCustomerRank.count}-->
                        -
                    <!--{else}-->
                        <a href="javascript:;" onclick="eccube.setModeAndSubmit('delete','id','<!--{$arrCustomerRank.rank_id}-->'); return false;">削除</a>
                    <!--{/if}-->
                </td>
            </tr>
        <!--{/foreach}-->
    </table>
</div>
