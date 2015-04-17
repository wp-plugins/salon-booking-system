Number.prototype.formatMoney = function (c, d, t) {
    var n = this,
        c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

jQuery(function ($) {
    if ($('#salon-step-services').length || $('#salon-step-secondary').length) {
        sln_serviceTotal($);
    }
    if ($('#salon-step-date').length) {
        sln_stepDate($);
    }

    // CHECKBOXES
    $('#sln-salon input:checkbox').each(function () {
        $(this).click(function() {
            $(this).parent().toggleClass("is-checked");
        })
    });
    // CHECKBOXES
});

function sln_stepDate($) {
    var isValid;
    var items = $('#salon-step-date').data('intervals');
        var func = function(){
        $('[data-ymd]').addClass('disabled');
        $.each(items.dates, function(key, value) {
           $('[data-ymd="'+value+'"]').removeClass('disabled');
        });
        $.each(items.times, function(key, value) {
           console.log(value);
           $('[data-ymd="'+value+'"]').removeClass('disabled');
        });
 
            return true; 
        }
        func();
        $('body').on('sln_date',func);

    function validate(obj, autosubmit) {
        var form = $(obj).closest('form');
        var validatingMessage = '<img src="' + salon.loading + '" alt="Loading ..." width="16" height="16" /> validating...';
        var data = form.serialize();
        data+= "&action=salon&method=checkDate&security="+salon.ajax_nonce;
        $('#sln-notifications').html(validatingMessage);
        $.ajax({
            url: salon.ajax_url,
            data: data,
            method: 'POST',
            dataType: 'json',
            success: function (data) {
                if (!data.success) {
                    var alertBox = $('<div class="alert alert-danger"></div>');
                    $(data.errors).each(function () {
                        alertBox.append('<p>').html(this);
                    });
                    $('#sln-notifications').html('').append(alertBox);
                    $('#sln-step-submit').attr('disabled', true);
                    isValid = false;
                } else {
                    $('#sln-step-submit').attr('disabled', false);
                    $('#sln-notifications').html('');
                    isValid = true;
                    if (autosubmit)
                        $('#sln-step-submit').click();
                }
                bindIntervals(data.intervals);
            }
        });
    }

    function bindIntervals(intervals){
//        putOptions($('#sln_date_day'), intervals.days, intervals.suggestedDay);
//        putOptions($('#sln_date_month'), intervals.months, intervals.suggestedMonth);
//        putOptions($('#sln_date_year'), intervals.years, intervals.suggestedYear);
        items = intervals;
        func();
        putOptions($('#sln_date'), intervals.suggestedDate);
        putOptions($('#sln_time'), intervals.suggestedTime);
    }

    function putOptions(selectElem, value){
        selectElem.val(value);
    }

    $('#sln_date, #sln_time').change(function () {
        validate(this,false);
    });
    $('#salon-step-date').submit(function () {
        if (!isValid) {
            validate(this, true);
            return false;
        }else{
            return true;
        }
    });

    initDatepickers($);
    initTimepickers($);
}

function sln_serviceTotal($) {
    var $checkboxes = $('.sln-service-list input[type="checkbox"]');
    var $totalbox = $('#services-total');

    function evalTot() {
        var tot = 0;
        $checkboxes.each(function () {
            if ($(this).is(':checked')) {
                tot += $(this).data('price');
            }
        });
        $totalbox.text(tot.formatMoney(2) + $totalbox.data('symbol'));
    }

    $checkboxes.click(function () {
        evalTot();
    });
    evalTot();
}

    function initDatepickers($) {
        $('.sln_datepicker input').each(function () {
            if ($(this).hasClass('started')) {
                return;
            } else {
                $(this)
                    .addClass('started')
                    .datetimepicker({
                    format: $(this).data('format'),
                    minuteStep: 60,
                    autoclose: true,
                    minView:2,
                    maxView:4,
                    todayBtn: true
                })
                 .on('show',function(){$('body').trigger('sln_date');})
                 .on('changeMonth',function(){$('body').trigger('sln_date');})
                 .on('changeYear',function(){$('body').trigger('sln_date');})
		;
            }
        });
    }
    function initTimepickers($) {
        $('.sln_timepicker input').each(function () {
            if ($(this).hasClass('started')) {
                return;
            } else {
                 var picker = $(this)
                    .addClass('started')
                    .datetimepicker({
                    format: $(this).data('format'),
                    minuteStep: $(this).data('interval'),
                    autoclose: true,
                    minView: 0,
                    maxView: 1,
                    startView: 1,
                })
                 .on('show',function(){$('body').trigger('sln_date');})
                 .on('changeMonth',function(){$('body').trigger('sln_date');})
                 .on('changeYear',function(){$('body').trigger('sln_date');})
                 .data('datetimepicker').picker;
               picker.addClass('timepicker');
            }
        });
    }
