<!--{foreach from=$arrColumns item=arrColumn}-->
    <!--{assign var=key value=$arrColumn.col}-->
    <!--{assign var=value value=$arrForm[$key]}-->
    <tr>
        <th><!--{$arrColumn.name|h}--></th>
        <td>
            <!--{if $arrColumn.type == 'textarea'}-->
                <!--{if $arrColumn.prefix}-->
                    <!--{$arrColumn.prefix|h}-->
                <!--{/if}-->
                <!--{$value|nl2br_html}-->
                <!--{if $arrColumn.suffix}-->
                    <!--{$arrColumn.suffix|h}-->
                <!--{/if}-->
            <!--{elseif $arrColumn.type == 'radio' || $arrColumn.type == 'select'}-->
                <!--{$arrSelect[$key][$value]|h}-->
            <!--{elseif $arrColumn.type == 'checkbox'}-->
                <!--{foreach from=$value item=value2}-->
                    <!--{if in_array($value2, $arrSelect)}-->
                        <!--{$arrSelect[$value2]|h}-->&nbsp;&nbsp;
                    <!--{/if}-->
                <!--{/foreach}-->
            <!--{elseif $arrColumn.type == 'date'}-->
                <!--{$value|sfDispDBDate:false|h}-->
            <!--{elseif $arrColumn.type == 'image'}-->
                <!--{if $arrForm.arrFile[$key].filepath != ""}-->
                    <img src="<!--{$arrForm.arrFile[$key].filepath}-->" /><br />
                <!--{/if}-->
            <!--{else}-->
                <!--{if $arrColumn.prefix}-->
                    <!--{$arrColumn.prefix|h}-->
                <!--{/if}-->
                <!--{$value|h}-->
                <!--{if $arrColumn.suffix}-->
                    <!--{$arrColumn.suffix|h}-->
                <!--{/if}-->
            <!--{/if}-->
        </td>
    </tr>
<!--{/foreach}-->
