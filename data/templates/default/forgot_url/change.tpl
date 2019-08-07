<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="パスワードを忘れた方(新しいパスワードの入力)"}-->

<div id="window_area">
    <h2>パスワードの再発行 新しいパスワードの入力</h2>
    <p class="information">
        新しいパスワードを入力してください。<br />
    </p>
    <p class="message">
        【重要】新しくパスワードを発行いたしますので、お忘れになったパスワードはご利用できなくなります。</p>
    </p>
    <form action="?" method="post" name="form1">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="change_confirm" />
        <input type="hidden" name="key" value="<!--{$smarty.request.key|h}-->" />

        <div id="completebox">
            <p>
                <span class="attention"><!--{$arrErr.password}--></span>
                パスワード <input type="password" name="password" value="" class="box300" style="<!--{$arrErr.password|sfGetErrorColor}-->" /></p>
            <p>
                <span class="attention"><!--{$arrErr.password02}--></span>
                パスワード(確認) <input type="password" name="password02" value="" class="box300" style="<!--{$arrErr.password02|sfGetErrorColor}-->" /></p>
        </div>

        <div class="btn_area">
            <ul>
                <li><input type="image" class="hover_change_image" src="<!--{$TPL_URLPATH}-->img/button/btn_next.jpg" alt="次へ" name="next" id="next" />
            </ul>
        </div>
    </form>
</div>

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->
