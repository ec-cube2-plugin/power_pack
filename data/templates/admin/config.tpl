<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

<script type="text/javascript">//<![CDATA[
    self.focus();
//]]></script>

<form name="form1" id="form1" method="post" action="#">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />

    <h2><!--{$tpl_subtitle|h}--></h2>

    <!--{foreach from=$arrConfigPath item=configPath}-->
        <!--{include file=$configPath}-->
    <!--{/foreach}-->

    <div class="btn-area">
        <a class="btn-action" href="javascript:;" onclick="eccube.fnFormModeSubmit('form1', 'edit', '', ''); return false;" name="subm"><span class="btn-next">この内容で登録する</span></a>
    </div>
</form>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
