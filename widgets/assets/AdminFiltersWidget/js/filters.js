(function($){
    $.fn.yzAdminFilters = {
        settings: {
            gridId : ''
        },
        filters: {},
        getData: function() {
            var data = {};
            $.each(this.filters, function(index,item){
                $.extend(data, item());
            });
            return data;
        },
        filter: function(){
            $.fn.yiiGridView.update(this.settings.gridId, {
                data:$.param(this.getData())
            });
        }
    };
})(jQuery);

$(function(){
    $('.admin-filters-widget form.admin-filters-widget-form').submit(function(){
        $.fn.yzAdminFilters.filter();
        return false;
    });
});