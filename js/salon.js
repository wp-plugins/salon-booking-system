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
    sln_init($);
});

function sln_init($) {
    if ($('#salon-step-services').length || $('#salon-step-secondary').length) {
        sln_serviceTotal($);
    }
    if ($('#salon-step-date').length) {
        sln_stepDate($);
    } else {
        $('[data-salon-toggle="next"]').click(function (e) {
            var form = $(this).closest('form');
            $('#sln-salon input.sln-invalid').removeClass('sln-invalid');
            if (form[0].checkValidity()) {
                sln_loadStep($, form.serialize() + '&' + $(this).data('salon-data'));
            }else{
                $('#sln-salon input:invalid').addClass('sln-invalid');
            }
            return false;
        });
    }
    $('[data-salon-toggle="direct"]').click(function (e) {
        e.preventDefault();
        sln_loadStep($, $(this).data('salon-data'));
        return false;
    });

    // CHECKBOXES
    $('#sln-salon input:checkbox').each(function () {
        $(this).click(function () {
            $(this).parent().toggleClass("is-checked");
        })
    });
    // RADIOBOXES
    $('#sln-salon input:radio').each(function () {
        $(this).click(function () {
            $(".is-checked").removeClass('is-checked');
            $(this).parent().toggleClass("is-checked");
        });
    });

}
function sln_loadStep($, data) {
    var loadingMessage = '<img src="' + salon.loading + '" alt="loading .." width="16" height="16" /> loading..';
    data += "&action=salon&method=salonStep&security=" + salon.ajax_nonce;
    $('#sln-notifications').html(loadingMessage);
    $.ajax({
        url: salon.ajax_url,
        data: data,
        method: 'POST',
        dataType: 'json',
        success: function (data) {
            if (typeof data.redirect != 'undefined') {
                window.location.href = data.redirect;
            } else {
                $('#sln-salon').replaceWith(data.content);
                salon.ajax_nonce = data.nonce;
                $('html, body').animate({
                    scrollTop: $("#sln-salon").offset().top
                }, 700);
                sln_init($);
            }
        },
        error: function(data){alert('error'); console.log(data);}
    });
}

function sln_stepDate($) {
    var isValid;
    var items = $('#salon-step-date').data('intervals');
    var func = function () {
        $('[data-ymd]').addClass('disabled');
        $.each(items.dates, function(key, value) {
           //console.log(value);
           $('[data-ymd="'+value+'"]').removeClass('disabled');
        });
        $.each(items.times, function(key, value) {
           //console.log(value);
           $('[data-ymd="'+value+'"]').removeClass('disabled');
        });

        return true;
    }
    func();
    $('body').on('sln_date', func);

    function validate(obj, autosubmit) {
        var form = $(obj).closest('form');
        var validatingMessage = '<img src="' + salon.loading + '" alt="loading .." width="16" height="16" /> '+salon.txt_validating;
        var data = form.serialize();
        data += "&action=salon&method=checkDate&security=" + salon.ajax_nonce;
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
                        submit();
                }
                bindIntervals(data.intervals);
            }
        });
    }

    function bindIntervals(intervals) {
//        putOptions($('#sln_date_day'), intervals.days, intervals.suggestedDay);
//        putOptions($('#sln_date_month'), intervals.months, intervals.suggestedMonth);
//        putOptions($('#sln_date_year'), intervals.years, intervals.suggestedYear);
        items = intervals;
        func();
        putOptions($('#sln_date'), intervals.suggestedDate);
        putOptions($('#sln_time'), intervals.suggestedTime);
    }

    function putOptions(selectElem, value) {
        selectElem.val(value);
    }

    function submit() {
        if ($('#sln-step-submit').data('salon-toggle').length)
            sln_loadStep($, $('#salon-step-date').serialize() + '&' + $('#sln-step-submit').data('salon-data'));
        else
            $('#sln-step-submit').click();
    }

    $('#sln_date, #sln_time').change(function () {
        validate(this, false);
    });
    $('#salon-step-date').submit(function () {
        if (!isValid) {
            validate(this, true);
        } else {
            submit();
        }
        return false;
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
        $totalbox.text($totalbox.data('symbol-left') + tot.formatMoney(2) + $totalbox.data('symbol-right'));
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
                    minView: 2,
                    maxView: 4,
                    todayBtn: true,
                    language: $(this).data('locale')
                })
                .on('show', function () {
                    $('body').trigger('sln_date');
                })
                .on('changeMonth', function () {
                    $('body').trigger('sln_date');
                })
                .on('changeYear', function () {
                    $('body').trigger('sln_date');
                })
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
                    minView: $(this).data('interval') == 60 ? 1: 0,
                    maxView: 1,
                    startView: 1,
                })
                .on('show', function () {
                    $('body').trigger('sln_date');
                })
                .on('changeMonth', function () {
                    $('body').trigger('sln_date');
                })
                .on('changeYear', function () {
                    $('body').trigger('sln_date');
                })
                .data('datetimepicker').picker;
            picker.addClass('timepicker');
        }
    });
}
