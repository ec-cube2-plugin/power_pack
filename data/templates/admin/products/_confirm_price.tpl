<!--{foreach from=$arrRanks item=arrRank}-->
    <!--{assign var=key value="price_rank_`$arrRank.id`"}-->
    <tr>
        <th><!--{$arrRank.name}-->価格</th>
        <td>
            <!--{if $arrForm[$key]}-->
                <!--{$arrForm[$key]|h}--> 円
            <!--{/if}-->
        </td>
    </tr>
<!--{/foreach}-->
