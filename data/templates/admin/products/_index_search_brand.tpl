<!--{assign var=key value="search_maker_id"}-->
<tr>
    <th><!--{$arrForm[$key].disp_name|h}--></th>
    <td colspan="3">
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
            <option value="" selected="selected">選択してください</option>
            <!--{html_options options=$arrMaker selected=$arrForm[$key].value}-->
        </select>
    </td>
</tr>
