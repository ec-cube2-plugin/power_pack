<!--{foreach from=$arrColumns item=arrColumn}-->
    <!--{assign var=key value=$arrColumn.col}-->
    <tr>
        <th><!--{$arrColumn.name|h}-->
            <!--{if $arrColumn.required}--><span class="attention"> *</span><!--{/if}-->
            <!--{if $arrColumn.type == 'image'}-->
                <br />[<!--{$arrColumn.width}-->×<!--{$arrColumn.height}-->]
            <!--{/if}-->
        </th>
        <td>
            <a name="<!--{$key}-->"></a>
            <!--{if $arrErr[$key]}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{/if}-->
            <!--{if $arrColumn.type == 'textarea'}-->
                <!--{if $arrColumn.prefix}-->
                    <!--{$arrColumn.prefix|h}-->
                <!--{/if}-->
                <textarea name="<!--{$key}-->" cols="60" rows="8" class="area60" maxlength="<!--{$arrColumn.length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{"\n"}--><!--{$arrForm[$key]|h}--></textarea>
                <!--{if $arrColumn.suffix}-->
                    <!--{$arrColumn.suffix|h}-->
                <!--{/if}-->
                <!--{if $arrColumn.length}-->
                    <br /><span class="attention"> (上限<!--{$arrColumn.length}-->文字)</span>
                <!--{/if}-->
            <!--{elseif $arrColumn.type == 'radio'}-->
                <span style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                    <!--{html_radios name=$key options=$arrSelect[$key] selected=$arrForm[$key] separator='&nbsp;&nbsp;'}-->
                </span>
            <!--{elseif $arrColumn.type == 'select'}-->
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                    <option value="" selected="selected">選択してください</option>
                    <!--{html_options options=$arrSelect[$key] selected=$arrForm[$key]}-->
                </select>
            <!--{elseif $arrColumn.type == 'checkbox'}-->
                <span style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                    <!--{html_checkboxes name=$key options=$arrSelect[$key] selected=$arrForm[$key] separator='&nbsp;&nbsp;'}-->
                </span>
            <!--{elseif $arrColumn.type == 'date'}-->
                <!--{assign var=key_year value="`$key`_year"}-->
                <!--{assign var=key_month value="`$key`_month"}-->
                <!--{assign var=key_day value="`$key`_day"}-->
                <select name="<!--{$key_year}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                    <option value="" selected="selected">----</option>
                    <!--{html_options options=$arrSelect[$key].year selected=$arrForm[$key_year]}-->
                </select>年
                <select name="<!--{$key_month}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$arrSelect[$key].month selected=$arrForm[$key_month]}-->
                </select>月
                <select name="<!--{$key_day}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$arrSelect[$key].day selected=$arrForm[$key_day]}-->
                </select>日
            <!--{elseif $arrColumn.type == 'image'}-->
                <!--{if $arrForm.arrFile[$key].filepath != ""}-->
                    <img src="<!--{$arrForm.arrFile[$key].filepath}-->" />　<a href="" onclick="selectAll('category_id'); eccube.setModeAndSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;">[画像の取り消し]</a><br />
                <!--{/if}-->
                <input type="file" name="<!--{$key}-->" size="40" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                <a class="btn-normal" href="javascript:;" name="btn" onclick="selectAll('category_id'); eccube.setModeAndSubmit('upload_image', 'image_key', '<!--{$key}-->'); return false;">アップロード</a>
            <!--{elseif $arrColumn.type == 'number'}-->
                <!--{if $arrColumn.prefix}-->
                    <!--{$arrColumn.prefix|h}-->
                <!--{/if}-->
                <input type="text" name="<!--{$key}-->" size="6" class="box6" value="<!--{$arrForm[$key]|h}-->" maxlength="<!--{$arrColumn.length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                <!--{if $arrColumn.suffix}-->
                    <!--{$arrColumn.suffix|h}-->
                <!--{/if}-->
                <!--{if $arrColumn.length}-->
                    <span class="attention"> (上限<!--{$arrColumn.length}-->文字)</span>
                <!--{/if}-->
            <!--{else}-->
                <!--{if $arrColumn.prefix}-->
                    <!--{$arrColumn.prefix|h}-->
                <!--{/if}-->
                <input type="text" name="<!--{$key}-->" size="60" class="box60" value="<!--{$arrForm[$key]|h}-->" maxlength="<!--{$arrColumn.length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                <!--{if $arrColumn.suffix}-->
                    <!--{$arrColumn.suffix|h}-->
                <!--{/if}-->
                <!--{if $arrColumn.length}-->
                    <span class="attention"> (上限<!--{$arrColumn.length}-->文字)</span>
                <!--{/if}-->
            <!--{/if}-->
            <!--{if $arrColumn.note}-->
                <br /><span class="attention"><!--{$arrColumn.note|h}--></span>
            <!--{/if}-->
        </td>
    </tr>
<!--{/foreach}-->
