<tr>
    <th>顧客ランク</th>
    <td>
    <!--{assign var=key value="search_customer_rank_id"}-->
    <!--{if is_array($arrSearchData[$key])}-->
        <!--{foreach item=item from=$arrSearchData[$key]}-->
            <!--{$arrRanks[$item]|h}-->　
        <!--{/foreach}-->
    <!--{else}-->(未指定)<!--{/if}-->
    </td>
</tr>
