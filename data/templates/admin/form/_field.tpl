<!--{if !$type}-->
    <!--{assign var=type value=$arrColumn.type}-->
<!--{/if}-->
<!--{if $type == 'textarea'}-->
    <!--{if $arrColumn.prefix}-->
        <!--{$arrColumn.prefix|h}-->
    <!--{/if}-->
    <textarea name="<!--{$arrColumn.name}-->" <!--{include file="form/_attr.tpl" attr=$arrColumn.attr}-->><!--{$arrColumn.value|h}--></textarea>
    <!--{if $arrColumn.suffix}-->
        <!--{$arrColumn.suffix|h}-->
    <!--{/if}-->
    <!--{if $arrColumn.max_length}-->
        <br /><span class="attention"> (上限<!--{$arrColumn.max_length}-->文字)</span>
    <!--{/if}-->
<!--{elseif $type == 'radio'}-->
    <span style="<!--{$arrColumn.error|sfGetErrorColor}-->">
        <!--{html_radios name=$arrColumn.name options=$arrColumn.choices selected=$arrColumn.value separator='&nbsp;&nbsp;'}-->
    </span>
<!--{elseif $type == 'select'}-->
    <select name="<!--{$arrColumn.name}-->" style="<!--{$arrColumn.error|sfGetErrorColor}-->">
        <!--{if !is_null($empty_data)}-->
            <option value=""><!--{$empty_data|default:'選択してください'|h}--></option>
        <!--{/if}-->
        <!--{html_options options=$arrColumn.choices selected=$arrColumn.value}-->
    </select>
<!--{elseif $type == 'checkbox'}-->
    <span style="<!--{$arrColumn.error|sfGetErrorColor}-->">
        <!--{html_checkboxes name=$arrColumn.name options=$arrColumn.choices selected=$arrColumn.value separator='&nbsp;&nbsp;'}-->
    </span>
<!--{elseif $type == 'date'}-->
    <!--{assign var=key_year value="`$arrColumn.name`_year"}-->
    <!--{assign var=key_month value="`$arrColumn.name`_month"}-->
    <!--{assign var=key_day value="`$arrColumn.name`_day"}-->
    <select name="<!--{$key_year}-->" style="<!--{$arrColumn.error|sfGetErrorColor}-->">
        <option value="" selected="selected">----</option>
        <!--{html_options options=$arrColumn.choices.year selected=$arrForm[$key_year]}-->
    </select>年
    <select name="<!--{$key_month}-->" style="<!--{$arrColumn.error|sfGetErrorColor}-->">
        <option value="" selected="selected">--</option>
        <!--{html_options options=$arrColumn.choices.month selected=$arrForm[$key_month]}-->
    </select>月
    <select name="<!--{$key_day}-->" style="<!--{$arrColumn.error|sfGetErrorColor}-->">
        <option value="" selected="selected">--</option>
        <!--{html_options options=$arrColumn.choices.day selected=$arrForm[$key_day]}-->
    </select>日
<!--{elseif $type == 'image'}-->
    <!--{if $arrColumn.filepath != ""}-->
        <img src="<!--{$arrColumn.filepath}-->" />　<a href="" onclick="selectAll('category_id'); eccube.setModeAndSubmit('delete_image', 'image_key', '<!--{$arrColumn.name}-->'); return false;">[画像の取り消し]</a><br />
    <!--{/if}-->
    <input type="file" name="<!--{$arrColumn.name}-->" size="40" style="<!--{$arrColumn.error|sfGetErrorColor}-->" />
    <a class="btn-normal" href="javascript:;" name="btn" onclick="selectAll('category_id'); eccube.setModeAndSubmit('upload_image', 'image_key', '<!--{$arrColumn.name}-->'); return false;">アップロード</a>
<!--{elseif $type == 'number'}-->
    <!--{if $arrColumn.prefix}-->
        <!--{$arrColumn.prefix|h}-->
    <!--{/if}-->
    <input type="number" name="<!--{$arrColumn.name}-->" <!--{include file="form/_attr.tpl" attr=$arrColumn.attr}--> value="<!--{$arrColumn.value|h}-->" style="<!--{$arrColumn.error|sfGetErrorColor}-->" />
    <!--{if $arrColumn.suffix}-->
        <!--{$arrColumn.suffix|h}-->
    <!--{/if}-->
    <!--{if $arrColumn.max_length}-->
        <span class="attention"> (上限<!--{$arrColumn.max_length}-->文字)</span>
    <!--{/if}-->
<!--{elseif $type == 'plain'}-->
    <!--{if $arrColumn.prefix}-->
        <!--{$arrColumn.prefix|h}-->
    <!--{/if}-->
    <!--{$arrColumn.value|h}-->
    <input type="hidden" name="<!--{$arrColumn.name}-->" value="<!--{$arrColumn.value|h}-->" />
    <!--{if $arrColumn.suffix}-->
        <!--{$arrColumn.suffix|h}-->
    <!--{/if}-->
<!--{else}-->
    <!--{if $arrColumn.prefix}-->
        <!--{$arrColumn.prefix|h}-->
    <!--{/if}-->
    <input type="text" name="<!--{$arrColumn.name}-->" <!--{include file="form/_attr.tpl" attr=$arrColumn.attr}--> value="<!--{$arrColumn.value|h}-->" style="<!--{$arrColumn.error|sfGetErrorColor}-->" />
    <!--{if $arrColumn.suffix}-->
        <!--{$arrColumn.suffix|h}-->
    <!--{/if}-->
    <!--{if $arrColumn.max_length}-->
        <span class="attention"> (上限<!--{$arrColumn.max_length}-->文字)</span>
    <!--{/if}-->
<!--{/if}-->
