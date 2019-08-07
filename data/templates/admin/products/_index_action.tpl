<div style="margin-bottom: 5px; padding: 10px; background: #f5f5f5;">
    チェックした商品を
    <!--{assign var=key value="action"}-->
    <span>
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
            <option value="">選択してください</option>
            <!--{html_options options=$arrAction selected=$arrForm[$key].value}-->
        </select>
    </span>
    <!--{assign var=key value="action_status"}-->
    <span class="<!--{$key}-->">
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
            <option value="">選択してください</option>
            <!--{html_options options=$arrDISP selected=$arrForm[$key].value}-->
        </select>
    </span>
    <!--{assign var=key value="action_maker"}-->
    <span class="<!--{$key}-->">
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
            <option value="">選択してください</option>
            <!--{html_options options=$arrMaker selected=$arrForm[$key].value}-->
        </select>
    </span>
    <!--{assign var=key value="action_category"}-->
    <span class="<!--{$key}-->">
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
            <option value="">選択してください</option>
            <!--{html_options options=$arrCatList selected=$arrForm[$key].value}-->
        </select>
    </span>
    <!--{assign var=key value="action_product_statuses"}-->
    <span class="<!--{$key}-->">
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
            <option value="">選択してください</option>
            <!--{html_options options=$arrSTATUS selected=$arrForm[$key].value}-->
        </select>
    </span>
    <a class="btn-tool" href="javascript:;" onclick="eccube.setModeAndSubmit('action','',''); return false;">実行</a>
</div>

<script type="text/javascript">
    $(function() {
        $("select[name='action']").change(function() {
            $(".action_status, .action_maker, .action_category, .action_product_statuses").hide();
            var action = $(this).find("option:selected").val();
            if (action == 'status_edit') {
                $(".action_status").show();
            } else if (action == 'maker_edit') {
                $(".action_maker").show();
            } else if (action == 'category_add' || action == 'category_del') {
                $(".action_category").show();
            } else if (action == 'product_statuses_add' || action == 'product_statuses_del') {
                $(".action_product_statuses").show();
            }
        }).change();
        $("input#action_product_id_all").change(function() {
            if ($(this).is(':checked')) {
                $("input.action_product_id").prop("checked", true);
            } else {
                $("input.action_product_id").prop("checked", false);
            }
        });
        $("input.action_product_id").change(function() {
            var action_size = $("input.action_product_id").size();
            var action_size_checked = $("input.action_product_id:checked").size();
            if (action_size === action_size_checked) {
                $("input#action_product_id_all").prop("checked", true);
            } else {
                $("input#action_product_id_all").prop("checked", false);
            }
        });
    });
</script>
