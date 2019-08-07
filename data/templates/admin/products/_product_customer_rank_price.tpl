<!--{foreach from=$arrColumns item=arrColumn}-->
    <!--{assign var=key value=$arrColumn.col}-->
    <tr>
        <th><!--{$smarty.const.SALE_PRICE_TITLE}--><span class="attention"> *</span></th>
        <td>
            <span class="attention"><!--{$arrErr.price02}--></span>
            <input type="text" name="price02" value="<!--{$arrForm.price02|h}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PRICE_LEN}-->" style="<!--{if $arrErr.price02 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>円
            <span class="attention"> (半角数字で入力)</span>
        </td>
    </tr>
<!--{/foreach}-->
