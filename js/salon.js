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
    var doingFunc = false;
    var func = function () {
        if(doingFunc) return;
        setTimeout(function(){
           doingFunc = true;
        $('[data-ymd]').addClass('disabled');
        $.each(items.dates, function(key, value) {
           //console.log(value);
           $('.day[data-ymd="'+value+'"]').removeClass('disabled');
        });

        $.each(items.times, function(key, value) {
           $('.hour[data-ymd="'+value+'"]').removeClass('disabled'); 
           $('.minute[data-ymd="'+value+'"]').removeClass('disabled'); 
           $('.hour[data-ymd="'+value.split(':')[0]+':00"]').removeClass('disabled');
        });
            doingFunc = false;
       },200);
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
                .on('place', function () {
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
                    showMeridian: $(this).data('meridian') ? true : false,
                })
                .on('show', function () {
                    $('body').trigger('sln_date');
                })
                .on('place', function () {
                    $('body').trigger('sln_date');
                })
 
               .data('datetimepicker').picker;
            picker.addClass('timepicker');
        }
    });
}
/* ========================================================================
 * Bootstrap: transition.js v3.2.0
 * http://getbootstrap.com/javascript/#transitions
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // CSS TRANSITION SUPPORT (Shoutout: http://www.modernizr.com/)
  // ============================================================

  function transitionEnd() {
    var el = document.createElement('bootstrap')

    var transEndEventNames = {
      WebkitTransition : 'webkitTransitionEnd',
      MozTransition    : 'transitionend',
      OTransition      : 'oTransitionEnd otransitionend',
      transition       : 'transitionend'
    }

    for (var name in transEndEventNames) {
      if (el.style[name] !== undefined) {
        return { end: transEndEventNames[name] }
      }
    }

    return false // explicit for ie8 (  ._.)
  }

  // http://blog.alexmaccaw.com/css-transitions
  $.fn.emulateTransitionEnd = function (duration) {
    var called = false
    var $el = this
    $(this).one('bsTransitionEnd', function () { called = true })
    var callback = function () { if (!called) $($el).trigger($.support.transition.end) }
    setTimeout(callback, duration)
    return this
  }

  $(function () {
    $.support.transition = transitionEnd()

    if (!$.support.transition) return

    $.event.special.bsTransitionEnd = {
      bindType: $.support.transition.end,
      delegateType: $.support.transition.end,
      handle: function (e) {
        if ($(e.target).is(this)) return e.handleObj.handler.apply(this, arguments)
      }
    }
  })

}(jQuery);

/* ========================================================================
 * Bootstrap: collapse.js v3.2.0
 * http://getbootstrap.com/javascript/#collapse
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // COLLAPSE PUBLIC CLASS DEFINITION
  // ================================

  var Collapse = function (element, options) {
    this.$element      = $(element)
    this.options       = $.extend({}, Collapse.DEFAULTS, options)
    this.transitioning = null

    if (this.options.parent) this.$parent = $(this.options.parent)
    if (this.options.toggle) this.toggle()
  }

  Collapse.VERSION  = '3.2.0'

  Collapse.DEFAULTS = {
    toggle: true
  }

  Collapse.prototype.dimension = function () {
    var hasWidth = this.$element.hasClass('width')
    return hasWidth ? 'width' : 'height'
  }

  Collapse.prototype.show = function () {
    if (this.transitioning || this.$element.hasClass('in')) return

    var startEvent = $.Event('show.bs.collapse')
    this.$element.trigger(startEvent)
    if (startEvent.isDefaultPrevented()) return

    var actives = this.$parent && this.$parent.find('> .panel > .in')

    if (actives && actives.length) {
      var hasData = actives.data('bs.collapse')
      if (hasData && hasData.transitioning) return
      Plugin.call(actives, 'hide')
      hasData || actives.data('bs.collapse', null)
    }

    var dimension = this.dimension()

    this.$element
      .removeClass('collapse')
      .addClass('collapsing')[dimension](0)

    this.transitioning = 1

    var complete = function () {
      this.$element
        .removeClass('collapsing')
        .addClass('collapse in')[dimension]('')
      this.transitioning = 0
      this.$element
        .trigger('shown.bs.collapse')
    }

    if (!$.support.transition) return complete.call(this)

    var scrollSize = $.camelCase(['scroll', dimension].join('-'))

    this.$element
      .one('bsTransitionEnd', $.proxy(complete, this))
      .emulateTransitionEnd(350)[dimension](this.$element[0][scrollSize])
  }

  Collapse.prototype.hide = function () {
    if (this.transitioning || !this.$element.hasClass('in')) return

    var startEvent = $.Event('hide.bs.collapse')
    this.$element.trigger(startEvent)
    if (startEvent.isDefaultPrevented()) return

    var dimension = this.dimension()

    this.$element[dimension](this.$element[dimension]())[0].offsetHeight

    this.$element
      .addClass('collapsing')
      .removeClass('collapse')
      .removeClass('in')

    this.transitioning = 1

    var complete = function () {
      this.transitioning = 0
      this.$element
        .trigger('hidden.bs.collapse')
        .removeClass('collapsing')
        .addClass('collapse')
    }

    if (!$.support.transition) return complete.call(this)

    this.$element
      [dimension](0)
      .one('bsTransitionEnd', $.proxy(complete, this))
      .emulateTransitionEnd(350)
  }

  Collapse.prototype.toggle = function () {
    this[this.$element.hasClass('in') ? 'hide' : 'show']()
  }


  // COLLAPSE PLUGIN DEFINITION
  // ==========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.collapse')
      var options = $.extend({}, Collapse.DEFAULTS, $this.data(), typeof option == 'object' && option)

      if (!data && options.toggle && option == 'show') option = !option
      if (!data) $this.data('bs.collapse', (data = new Collapse(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.collapse

  $.fn.collapse             = Plugin
  $.fn.collapse.Constructor = Collapse


  // COLLAPSE NO CONFLICT
  // ====================

  $.fn.collapse.noConflict = function () {
    $.fn.collapse = old
    return this
  }


  // COLLAPSE DATA-API
  // =================

  $(document).on('click.bs.collapse.data-api', '[data-toggle="collapse"]', function (e) {
    var href
    var $this   = $(this)
    var target  = $this.attr('data-target')
        || e.preventDefault()
        || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '') // strip for ie7
    var $target = $(target)
    var data    = $target.data('bs.collapse')
    var option  = data ? 'toggle' : $this.data()
    var parent  = $this.attr('data-parent')
    var $parent = parent && $(parent)

    if (!data || !data.transitioning) {
      if ($parent) $parent.find('[data-toggle="collapse"][data-parent="' + parent + '"]').not($this).addClass('collapsed')
      $this[$target.hasClass('in') ? 'addClass' : 'removeClass']('collapsed')
    }

    Plugin.call($target, option)
  })

}(jQuery);

