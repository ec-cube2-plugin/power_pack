<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-XXXXXXXX-X');
    ga('require', 'displayfeatures');
    ga('require', 'linkid', 'linkid.js');
    ga('send', 'pageview');
    <!--{if $smarty.session.customer}-->
        ga('set', '&uid', '<!--{$smarty.session.customer.customer_id|h}-->');
    <!--{/if}-->

    <!--{if $arrOrder && $arrOrderDetails}-->
        ga('require', 'ecommerce', 'ecommerce.js');
        ga('ecommerce:addTransaction', {
            'id': '<!--{$arrOrder.order_id|h}-->',
            'affiliation': '',
            'revenue': '<!--{$arrOrder.total|h}-->',
            'shipping': '<!--{$arrOrder.deliv_fee|h}-->',
            'tax': '<!--{$arrOrder.tax|h}-->'
        });
        <!--{foreach from=$arrOrderDetails item=arrOrderDetail}-->
            ga('ecommerce:addItem', {
                'id': '<!--{$arrOrder.order_id|h}-->',
                'name': '<!--{$arrOrderDetail.product_name|h}-->',
                'sku': '<!--{$arrOrderDetail.product_code|h}-->',
                'category': '<!--{$arrOrderDetail.classcategory_name1|h}--><!--{if $arrOrderDetail.classcategory_name1 && $arrOrderDetail.classcategory_name2}--> <!--{/if}--><!--{$arrOrderDetail.classcategory_name2|h}-->',
                'price': '<!--{$arrOrderDetail.price|h}-->',
                'quantity': '<!--{$arrOrderDetail.quantity|h}-->'
            });
        <!--{/foreach}-->
        ga('ecommerce:send');
    <!--{/if}-->
</script>
