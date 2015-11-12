<link rel="stylesheet" href="<?php echo SLN_PLUGIN_URL ?>/css/calendar.css">
<script type="text/javascript" src="<?php echo SLN_PLUGIN_URL ?>/js/calendar_language/<?php echo str_replace('_','-',get_locale())?>.js"></script>
<script type="text/javascript" src="<?php echo SLN_PLUGIN_URL ?>/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo SLN_PLUGIN_URL ?>/js/underscore-min.js"></script>
<script type="text/javascript" src="<?php echo SLN_PLUGIN_URL ?>/js/calendar.js"></script>
<script type="text/javascript" src="<?php echo SLN_PLUGIN_URL ?>/js/calendar-app.js"></script>
<script>
<?php $today = new DateTime()?>
jQuery(function($){
    initSalonCalendar(
        $,
        salon.ajax_url+"&action=salon&method=calendar&security="+salon.ajax_nonce,
//        '<?php echo SLN_PLUGIN_URL ?>/js/events.json.php',
        '<?php echo $today->format('Y-m-d') ?>',
        '<?php echo SLN_PLUGIN_URL ?>/views/js/calendar/'
    );
});
</script>
<div class="wrap sln-bootstrap">
    <h1><?php _e('Calendar','sln')?></h1>
</div>
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-8">
<div class="page-header pull-left">
<h3></h3>
</div>
        <div class="form-inline pull-right">
            <div class="btn-group">
	<button class="btn btn-primary" data-calendar-nav="prev">&laquo; <?php _e('Previous') ?></button>
	<button class="btn btn-default" data-calendar-nav="today"><?php _e('Today')?></button>
	<button class="btn btn-primary" data-calendar-nav="next"><?php _e('Next') ?> &raquo;</button>
<?php /*
</div>
<div class="btn-group">
*/ ?>
	<button class="btn btn-warning" data-calendar-view="year"><?php _e('Year')?></button>
	<button class="btn btn-warning active" data-calendar-view="month"><?php _e('Month')?></button>
	<button class="btn btn-warning" data-calendar-view="week"><?php _e('Week')?></button>
	<button class="btn btn-warning" data-calendar-view="day"><?php _e('Day')?></button>
        <a href="<?php echo get_admin_url()?>edit.php?post_type=sln_booking" class="pull-right btn btn-warning" id="calendar-add-new">Add new</a>
            </div>
        </div>
        <div class="clearfix"></div>
        <div id="calendar"></div>
    </div>
    <div class="col-md-3 sln-calendar-sidebar">
        <h3>Bookings</h3>
        <ul id="eventlist" class="nav nav-list"></ul>
        <div class="legend">
<h4><?php _e('Legend','sln')?></h4> 
<ul>
<li><span class="pull-left event event-warning"></span><?php echo SLN_Enum_BookingStatus::getLabel(SLN_Enum_BookingStatus::PENDING) ?></li> 
<li><span class="pull-left event event-success"></span><?php echo SLN_Enum_BookingStatus::getLabel(SLN_Enum_BookingStatus::PAID) ?> <?php _e('or','sln')?> <?php echo SLN_Enum_BookingStatus::getLabel(SLN_Enum_BookingStatus::CONFIRMED)?></li>
<li><span class="pull-left event event-info"></span><?php echo SLN_Enum_BookingStatus::getLabel(SLN_Enum_BookingStatus::PAY_LATER) ?></li>
<li><span class="pull-left event event-danger"></span><?php echo SLN_Enum_BookingStatus::getLabel(SLN_Enum_BookingStatus::CANCELED) ?></li>
</ul>
<div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="clearfix"></div>

