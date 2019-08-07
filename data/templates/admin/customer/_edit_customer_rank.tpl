 <tr>
    <th>顧客ランク<span class="attention"> *</span></th>
    <td>
        <span class="attention"><!--{$arrErr.customer_rank_id}--></span>
        <select name="customer_rank_id" <!--{if $arrErr.customer_rank_id != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->>
                <!--{html_options options=$arrRanks selected=$arrForm.customer_rank_id}-->
        </select>
    </td>
</tr>
