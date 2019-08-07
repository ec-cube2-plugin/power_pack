<style type="text/css">
    #navi ul,
    #footer {
        z-index: 99;
    }
    .ace_box {
        padding: 0;
    }
    input[name=area_row] + div.btn,
    input[name=html_area_row] + br {
        display: none;
    }
</style>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->plugin/PowerPack/js/jquery-ace.min.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->plugin/PowerPack/js/ace/ace.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->plugin/PowerPack/js/ace/mode-smarty.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->plugin/PowerPack/js/ace/mode-css.js"></script>
<script type="text/javascript">
    var aceDecoratorsSmarty = $('textarea.smarty, textarea#tpl_data, textarea#bloc_html, textarea#header-area, textarea#footer-area').ace({ lang: 'smarty', width: 'auto' });
    aceDecoratorsSmarty.each(function(idx, aceDecorator){
        var ace = $(aceDecorator).data('ace').editor.ace;
        ace.setOptions({
            minLines: 3,
            maxLines: 'Infinity',
            wrap: true
        });
    });
    var aceDecoratorsCss = $('textarea.css, textarea#css').ace({ lang: 'css', width: 'auto' });
    aceDecoratorsCss.each(function(idx, aceDecorator){
        var ace = $(aceDecorator).data('ace').editor.ace;
        ace.setOptions({
            minLines: 3,
            maxLines: 'Infinity',
            wrap: true
        });
    });
    $('textarea.smarty, textarea.css, textarea#tpl_data, textarea#bloc_html, textarea#css, textarea#header-area, textarea#footer-area').closest('td').addClass('ace_box');
    $('#resize-btn, #header-area-resize-btn, #footer-area-resize-btn').remove();
</script>

