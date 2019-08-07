<!--{foreach from=$arrColumns item=arrColumn}-->
    <!--{assign var=key value="search_`$arrColumn.col`"}-->
    <!--{if $arrColumn.searchable}-->
        <tr>
            <th><!--{$arrForm[$key].disp_name|h}--></th>
            <td colspan="3">
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{if $arrColumn.searchable == 3}-->
                    <!--{include file="products/index_`$arrColumn.col`.tpl"}-->
                <!--{elseif $arrColumn.type == 'select'}-->
                    <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                        <option value="" selected="selected">選択してください</option>
                        <!--{html_options options=$arrSelect[$arrColumn.col] selected=$arrForm[$key].value}-->
                    </select>
                <!--{else}-->
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
                <!--{/if}-->
            </td>
        </tr>
    <!--{/if}-->
<!--{/foreach}-->
