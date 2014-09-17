define([
    "jquery",
    "jquery/ui",
], function($){
    $.widget('sample_news.treeDisplay', {
        _create: function(){
            var tree = $(this.element);
            if(tree){
                tree.addClass('sample-news-tree');
                tree.find('ul').hide();
                tree.find('li').each(function(){
                    var children = $(this).children('ul');
                    var that = this;
                    if (children.length > 0) {
                        var span = $('<span></span>').addClass('collapsed');
                        span.on('click', function(){
                            if ($(this).hasClass('collapsed')){
                                $(this).addClass('expanded');
                                $(this).removeClass('collapsed');
                                $(that).children('ul').slideDown();
                            }
                            else{
                                $(this).removeClass('expanded');
                                $(this).addClass('collapsed');
                                $(that).children('ul').slideUp();
                            }
                        });
                        $(this).prepend(span);
                    }
                });
            };
        }
    });
});