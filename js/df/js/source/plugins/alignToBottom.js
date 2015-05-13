;(function($){
    $.fn.alignBottom = function() 
    {

        var defaults = {
            outerHight: 0,
            elementHeight: 0
        };

        var options = $.extend(defaults, options);
        var bpHeight = 0; // Border + padding

        return this.each(function() 
        {
            options.outerHight = $(this).parent().outerHeight();
            bpHeight = options.outerHight - $(this).parent().height();
            options.elementHeight = $(this).outerHeight(true) + bpHeight;


            $(this).css({'position':'relative','top':options.outerHight-(options.elementHeight)+'px'});
        });
    };
})(jQuery);