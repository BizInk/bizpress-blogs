/* Bizpress Client JS */
(function($){
    $('.bizpress-blog').bind('copy paste',function(e) {
        e.preventDefault(); 
        e.stopPropagation();
        return false; 
    });
})(jQuery);