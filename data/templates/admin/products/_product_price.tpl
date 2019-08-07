<!--{foreach from=$arrRanks item=arrRank}-->
    <!--{assign var=key value="price_rank_`$arrRank.id`"}-->
    <tr>
        <th><!--{$arrRank.name}-->価格</th>
        <td>
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PRICE_LEN}-->" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>円
            <span class="attention"> (半角数字で入力)</span>
        </td>
    </tr>
<!--{/foreach}-->
