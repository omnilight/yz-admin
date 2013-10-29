function Admin()
{
    this.setFlashUrl = null;
    this.redirectUrl = null;
    this.fileManagerUrl = null;
    this.fileManagerAvailable = false;
}

Admin.prototype.setFlash = function(type, message, callback) {
    $.ajax({
        type: 'post',
        url: this.setFlashUrl,
        data: {
            type: type,
            message: message
        },
        success: callback
    });
};

Admin.prototype.redirect = function(route) {
    if(typeof route == 'string') {
        window.location = route;
    } else {
        var params = {
            route: route[0],
            params: route.slice(1)
        };
        var url = $.param.querystring(this.redirectUrl,params);
        window.location = url;
    }
};

Admin.prototype.showFileManager = function() {
    if(this.fileManagerAvailable) {
        var fileWindow = window.open(this.fileManagerUrl, 'fileManager',
            "width=1000,height=600,resizable=yes,scrollbars=no,status=no"

        )
    } else {
        alert('File manager is currently disabled or not exist');
    }
};

$.admin = new Admin();

$(function(){
    $('.toggleLink').click(function(){
       $($(this).data('element')).toggle();
       return false;
    });
    $('body').tooltip({'selector':'.tooltiped'});

    if($('iframe.fullPage').size()) {
        function _fullSizer() {
            $('iframe.fullPage').height($(window).innerHeight() - $('iframe.fullPage').offset().top);
        }
        $(window).resize(_fullSizer);
        _fullSizer();
    }
});