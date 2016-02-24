var autocomplete = autocomplete || {};

(function($) {
    function Autocomplete(options) {
        this.min_length = options.hasOwnProperty('min_length') ? options.min_length : 4;
    }

    Autocomplete.prototype.init = function() {
        var that = this;
        $('[data-type="combo_box"]').each(function() {
            $(this).keyup(function() {
                if (that.min_length <= this.value.length) {
                    that.call(this);
                }
            });
        });
    };

    Autocomplete.prototype.call = function(input) {
        data = {field: input.name, search: input.value};
        $.ajax({
            url: $(input).data('url'),
            type: 'post',
            dataType: 'json',
            contentType: 'application/json',
            async: true,
            data: JSON.stringify(data),
            success: function(data, textStatus, jqXHR) {
                if (0 < Object.keys(data.records).length) {
                    $.each(data.records, function(value, name) {
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    };

    autocomplete = new Autocomplete([]);

    autocomplete.init();

})(jQuery);