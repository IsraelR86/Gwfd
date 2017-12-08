'use strict';

$(document).ready(function() {
    if (Cookies.get('status-sidebar') == 'active') {
        $("#menu-toggle").trigger('click');
    }
    
    $('.chartPuntaje').easyPieChart({
        easing: 'easeOutBounce',
        lineWidth: 5,
        onStep: function(from, to, percent) {
            $(this.el).find('.percent').text(Math.round(percent));
        }
    });
    
});