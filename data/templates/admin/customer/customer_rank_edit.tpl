<form name="form1" id="form1" method="post" action="?id=<!--{$form.id.value}-->">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="complete" />

    <div id="customer" class="contents-main">
        <!--{include file="form/_form.tpl" form=$form}-->

        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="./customer_rank.php" ><span class="btn-prev">戻る</span></a></li>
                <li><a class="btn-action" href="javascript:;" onclick="eccube.setModeAndSubmit('complete','',''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
            </ul>
        </div>
    </div>
</form>
