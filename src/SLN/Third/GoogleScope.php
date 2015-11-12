<?php
if (!isset($_SESSION))
    session_start();

function _pre($m) {
    echo "<pre>";
    print_r($m);
    echo "</pre>";
}

function sln_my_wp_log($message, $file = null, $level = 1) {
    // full path to log file
    if ($file == null) {
        $file = 'debug.log';
    }

    $file = SLN_PLUGIN_DIR . DIRECTORY_SEPARATOR . "src/SLN/Third/" . $file;

    /* backtrace */
    $bTrace = debug_backtrace(); // assoc array

    /* Build the string containing the complete log line. */
    $line = PHP_EOL . sprintf('[%s, <%s>, (%d)]==> %s', date("Y/m/d h:i:s" /* ,time() */), basename($bTrace[0]['file']), $bTrace[0]['line'], print_r($message, true));

    if ($level > 1) {
        $i = 0;
        $line.=PHP_EOL . sprintf('Call Stack : ');
        while (++$i < $level && isset($bTrace[$i])) {
            $line.=PHP_EOL . sprintf("\tfile: %s, function: %s, line: %d" . PHP_EOL . "\targs : %s", isset($bTrace[$i]['file']) ? basename($bTrace[$i]['file']) : '(same as previous)', isset($bTrace[$i]['function']) ? $bTrace[$i]['function'] : '(anonymous)', isset($bTrace[$i]['line']) ? $bTrace[$i]['line'] : 'UNKNOWN', print_r($bTrace[$i]['args'], true));
        }
        $line.=PHP_EOL . sprintf('End Call Stack') . PHP_EOL;
    }
    // log to file
    SLN_Plugin::addLog($line);
    return true;
 
    file_put_contents($file, $line, FILE_APPEND);

    return true;
}

//Add to htaccess RewriteRule ^wp-admin/salon-settings/(.*)/$ /wp-admin/admin.php?page=salon-settings&tab=$1 [L]
//if (!file_exists(SLN_PLUGIN_DIR . '/src/SLN/Third/google-api-php-client/src/Google/autoload.php')) die('error');
require SLN_PLUGIN_DIR . '/src/SLN/Third/google-api-php-client/src/Google/autoload.php';
require_once SLN_PLUGIN_DIR . '/src/SLN/Third/google-api-php-client/src/Google/Client.php';
require_once SLN_PLUGIN_DIR . '/src/SLN/Third/google-api-php-client/src/Google/Service/Calendar.php';

/**
 * Add '_sln_calendar_event_id' => '' in MetBox/Booking.php getFieldList
 */
class SLN_GoogleScope {

    public $date_offset = 0;
    public $client_id = '102246196260-so9c267umku08brmrgder71ige08t3nm.apps.googleusercontent.com'; //change this
    public $email_address = '102246196260-so9c267umku08brmrgder71ige08t3nm@developer.gserviceaccount.com'; //change this    
    public $scopes = "https://www.googleapis.com/auth/calendar";
    public $key_file_location = 'prv.p12'; //change this           
    public $outh2_client_id = "102246196260-hjpu1fs2rh5b9mesa9l5urelno396vc0.apps.googleusercontent.com";
    public $outh2_client_secret = "AJzLfWtRDz53JLT5fYp5gLqZ";
    public $outh2_redirect_uri;
    public $google_calendar_enabled = false;
    public $google_client_calendar;
    public $client;
    public $service;
    public $settings;

    /**
     * __construct
     */
    public function __construct() {
        if (is_admin()) {
            add_action('wp_ajax_googleoauth-callback', array($this, 'get_client'));
            add_action('wp_ajax_nopriv_googleoauth-callback', array($this, 'get_client'));
            add_action('wp_ajax_startsynch', array($this, 'start_synch'));
            add_action('wp_ajax_deleteallevents', array($this, 'delete_all_bookings_event'));
            add_action('admin_footer', array($this, 'add_script'));
        }
    }

    public function add_script() {
        ?>
        <script>
            jQuery(document).ready(function () {
                jQuery('#sln_synch').click(function () {
                    var $button = jQuery(this);
                    $button.addClass('disabled').after('<div class="load-spinner"><img src="<?php echo get_site_url() . '/wp-admin/images/wpspin_light.gif'; ?>" /></div>');
                    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
                    var data = {action: 'startsynch'};
                    jQuery.post(ajaxurl, data, function (response) {
                        if (response == 'OK') {
                            alert("<?php echo __('Operation completed!', 'sln'); ?>");
                        } else {
                            var tmp = data.split('|');
                            if (tmp[1])
                                alert(tmp[1]);
                        }
                        $button.removeClass('disabled');
                        jQuery('.load-spinner').remove();
                    });
                });
                jQuery('#sln_del').click(function () {
                    var $button = jQuery(this);
                    $button.addClass('disabled').after('<div class="load-spinner"><img src="<?php echo get_site_url() . '/wp-admin/images/wpspin_light.gif'; ?>" /></div>');
                    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
                    var data = {action: 'deleteallevents'};
                    jQuery.post(ajaxurl, data, function (response) {
                        if (response == 'OK') {
                            alert("<?php echo __('Operation completed!', 'sln'); ?>");
                        } else {
                            var tmp = data.split('|');
                            if (tmp[1])
                                alert(tmp[1]);
                        }
                        $button.removeClass('disabled');
                        jQuery('.load-spinner').remove();
                    });
                });
            });
        </script>
        <?php
    }

    public function start_synch() {
        if (!$this->is_connected()) {
            echo "KO|" . __("Google Client is not connected!");
        }

        $now = new DateTime('NOW');
        $booking_handler = new SLN_Bookings_Handle($now);
        $bookings = $booking_handler->getBookings();
        foreach ($bookings as $k => $post) {
            $event_id = get_post_meta($post->ID, '_sln_calendar_event_id', true);
            $this->delete_event_from_booking($event_id);
        }

        foreach ($bookings as $k => $post) {
            synch_a_booking($post->ID, $post, true);
        }
        echo "OK";
        die();
    }

    public function delete_all_bookings_event($bookings = "") {
        $now = new DateTime('NOW');
        $booking_handler = new SLN_Bookings_Handle($now);
        $bookings = $booking_handler->getBookings();

        foreach ($bookings as $k => $post) {
            $event_id = get_post_meta($post->ID, '_sln_calendar_event_id', true);
            $this->delete_event_from_booking($event_id);
        }
        echo "OK";
        die;
    }

    /**
     * wp_init
     * @param type $plugin
     */
    public function wp_init() {

        $this->google_calendar_enabled = $this->settings->get('google_calendar_enabled');
        $this->google_client_calendar = $this->settings->get('google_client_calendar');

        if (isset($this->settings)) {
            $this->outh2_client_id = $this->settings->get('google_outh2_client_id');
            $this->outh2_client_secret = $this->settings->get('google_outh2_client_secret');
            $this->outh2_redirect_uri = $this->settings->get('google_outh2_redirect_uri');
            if (/* $this->google_calendar_enabled && */
                    (!empty($this->outh2_client_id) &&
                    !empty($this->outh2_client_secret) &&
                    !empty($this->outh2_redirect_uri))
            ) {
                if ($this->google_calendar_enabled)
                    $this->start_auth();
                else
                    $this->start_auth(true);
            }
        }
    }

    /**
     * start_auth
     */
    public function start_auth($force_revoke_token = false) {
        $access_token = (isset($_SESSION['access_token']) && !empty($_SESSION['access_token'])) ? $_SESSION['access_token'] : "";
        if (!isset($access_token) || empty($access_token))
            $access_token = $this->settings->get('sln_access_token');

        if (isset($access_token) && !empty($access_token)) {
            if ($force_revoke_token || (isset($_GET['revoketoken']) && $_GET['revoketoken'] == 1)/* || $client->isAccessTokenExpired() */) {
                $res = wp_remote_get("https://accounts.google.com/o/oauth2/revoke?token={$access_token}");
                $this->settings->set('sln_refresh_token', '');
                $this->settings->set('sln_access_token', '');
                $this->settings->save();
                unset($_SESSION['access_token']);
                header("Location: " . admin_url('admin.php?page=salon-settings&tab=gcalendar'));
            }

            $this->client = new Google_Client();
            $this->client->setClientId($this->outh2_client_id);
            $this->client->setClientSecret($this->outh2_client_secret);
            $this->client->setRedirectUri(isset($this->outh2_redirect_uri) ? $this->outh2_redirect_uri : admin_url('admin-ajax.php?action=googleoauth-callback'));
            $this->client->setAccessType('offline');
            //$this->client->setApplicationName('wpchief-calendar');
            //$client->setIncludeGrantedScopes(true);
            $this->client->addScope($this->scopes);
            $this->client->setAccessToken($access_token);

            $this->service = $this->get_google_service();
        } else {
            if (!$force_revoke_token) {
                $loginUrl = 'https://accounts.google.com/o/oauth2/auth?response_type=code&client_id=' . $this->outh2_client_id . '&redirect_uri=' . $this->outh2_redirect_uri . '&scope=' . $this->scopes . '&access_type=offline&approval_prompt=force';
                header("Location: " . $loginUrl);
            }
        }
    }

    /**
     * get_client
     */
    public function get_client() {
        if (isset($_GET['error'])) {
            header("Location: " . admin_url('admin.php?page=salon-settings&tab=gcalendar&revoketoken=1'));
        }

        $code = isset($_GET['code']) ? $_GET['code'] : null;

        if (isset($code)) {
            $oauth_result = wp_remote_post("https://accounts.google.com/o/oauth2/token", array(
                'body' => array(
                    'code' => $code,
                    'client_id' => $this->outh2_client_id,
                    'client_secret' => $this->outh2_client_secret,
                    'redirect_uri' => isset($this->outh2_redirect_uri) ? $this->outh2_redirect_uri : admin_url('admin-ajax.php?action=googleoauth-callback'),
                    'grant_type' => 'authorization_code'
                )
            ));

            if (!is_wp_error($oauth_result)) {
                $oauth_response = json_decode($oauth_result['body'], true);
            } else {
                _pre($oauth_result);
                die();
            }

            if (isset($oauth_response['access_token'])) {
                //refresh_token is present with only login setting approval_prompt=force
                $oauth_refresh_token = $oauth_response['refresh_token'];
                $oauth_token_type = $oauth_response['token_type'];
                $oauth_access_token = $oauth_response['access_token'];
                $oauth_expiry = $oauth_response['expires_in'] + current_time('timestamp', true);
                $idtoken_validation_result = wp_remote_get('https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . $oauth_access_token);

                $_SESSION['access_token'] = $oauth_result['body']; //$oauth_access_token;
                $_SESSION['refresh_token'] = $oauth_refresh_token;
                $this->settings->set('sln_access_token', $oauth_result['body']/* $oauth_access_token */);
                $this->settings->set('sln_refresh_token', $oauth_refresh_token);
                $this->settings->save();

                if (!is_wp_error($idtoken_validation_result)) {
                    $idtoken_response = json_decode($idtoken_validation_result['body'], true);
                    setcookie('google_oauth_id_token', $oauth_access_token, $oauth_expiry, COOKIEPATH, COOKIE_DOMAIN);
                    //setcookie('google_oauth_username', $oauth_username, (time() + ( 86400 * 7)), COOKIEPATH, COOKIE_DOMAIN);
                } else {
                    _pre($idtoken_validation_result);
                    die();
                }
            } else {
                _pre($oauth_response);
                if (isset($oauth_response['error'])) {
                    header("Location: " . admin_url('admin.php?page=salon-settings&tab=gcalendar&revoketoken=1'));
                }
                die();
            }
        } else {
            $this->start_auth();
        }
        header("Location: " . admin_url('admin.php?page=salon-settings&tab=gcalendar'));
    }

    /**
     * set_settings_by_plugin
     * @param type $plugin
     */
    public function set_settings_by_plugin($plugin) {
        $this->settings = $plugin->getSettings();
    }

    /**
     * get_google_service creates and return the google service
     * @return \Google_Service_Calendar
     */
    public function get_google_service() {
        return new Google_Service_Calendar($this->client);
    }

    /**
     * start_auth init the login to google services
     */
    public function start_auth_assertion() {
        $base = SLN_PLUGIN_DIR . "/src/SLN/Third/";
        $key = file_get_contents($base . $this->key_file_location);
        $cred = new Google_Auth_AssertionCredentials(
                $this->email_address, array($this->scopes), $key
        );
        $this->client->setAssertionCredentials($cred);
        if ($this->client->getAuth()->isAccessTokenExpired()) {
            $this->client->getAuth()->refreshTokenWithAssertion($cred);
        }
    }

    /**
     * is_connected
     * @return boolean
     */
    public function is_connected() {
        $ret = (isset($this->client) && !$this->client->getAuth()->isAccessTokenExpired());
        sln_my_wp_log("is connected " . $ret);
        sln_my_wp_log("client " . isset($this->client));

        if (!$ret) {
            $access_token = (isset($_SESSION['access_token']) && !empty($_SESSION['access_token'])) ? $_SESSION['access_token'] : "";
            if (!isset($access_token) || empty($access_token)) {
                $access_token = $this->settings->get('sln_access_token');
            }
            sln_my_wp_log($access_token);

            $refresh_token = isset($_SESSION['refresh_token']) ? $_SESSION['refresh_token'] : "";
            if (!isset($refresh_token) || empty($refresh_token)) {
                $refresh_token = $this->settings->get('sln_refresh_token');
            }
            sln_my_wp_log($refresh_token);

            try {
                if (isset($this->client)) {
                    sln_my_wp_log("Refreshed Token");
                    $this->client->refreshToken($refresh_token);
                    sln_my_wp_log($refresh_token);
                } else {
                    sln_my_wp_log("Not Refreshed Token");

                    $this->client = new Google_Client();
                    $this->client->setClientId($this->outh2_client_id);
                    $this->client->setClientSecret($this->outh2_client_secret);
                    $this->client->setRedirectUri(isset($this->outh2_redirect_uri) ? $this->outh2_redirect_uri : admin_url('admin-ajax.php?action=googleoauth-callback'));
                    $this->client->setAccessType('offline');
                    //$this->client->setApplicationName('wpchief-calendar');
                    //$client->setIncludeGrantedScopes(true);
                    $this->client->addScope($this->scopes);
                    $this->client->setAccessToken($access_token);
                    $this->client->refreshToken($refresh_token);

                    $this->service = $this->get_google_service();

                    sln_my_wp_log("is connected2 :" . $this->client->getAuth()->isAccessTokenExpired());
                }
                return true;
            } catch (Exception $e) {
                sln_my_wp_log($e);
                return false;
            }
            return true;
        }
        return $ret;
    }

    /**
     * get_calendar_list
     * @return type
     */
    public function get_calendar_list() {
        $cal_list = array();

        if (!$this->is_connected())
            return $cal_list;

        $calendarList = $this->service->calendarList->listCalendarList();
        $cal_list = array();
        $cal_list['']['id'] = 0;
        $cal_list['']['label'] = __("Scegli tra i tuoi calendari", 'sln');
        while (true) {
            foreach ($calendarList->getItems() as $calendarListEntry) {
                if (!isset($cal_list[$calendarListEntry->getId()]))
                    $cal_list[$calendarListEntry->getId()] = array();
                $cal_list[$calendarListEntry->getId()]['id'] = $calendarListEntry->getId();
                $cal_list[$calendarListEntry->getId()]['label'] = $calendarListEntry->getSummary();
            }
            $pageToken = $calendarList->getNextPageToken();
            if ($pageToken) {
                $optParams = array('pageToken' => $pageToken);
                $calendarList = $this->service->calendarList->listCalendarList($optParams);
            } else {
                break;
            }
        }

        return $cal_list;
    }

    /**
     * create_event
     * @param type $params
      array(
      'email' => $email,
      'title' => $title,
      'location' => $location,
      'date_start' => $date_start,
      'time_start' => $time_start,
      'date_end' => $date_end,
      'time_end' => $time_end,
      );
     * @return type
     */
    public function create_event($params) {
        extract($params);

        $catId = isset($params['catId']) && !empty($params['catId']) ? $params['catId'] : "primary";

        $event = new Google_Service_Calendar_Event();
        $event->setSummary($title);
        $event->setLocation($location);
        $start = new Google_Service_Calendar_EventDateTime();
        //$dateTimeS = gtime(set_data($date_start), $time_start);
        //$dateTimeS = date(DATE_ATOM, strtotime($date_start . " " . $time_start));
        $str_date = strtotime($date_start . " " . $time_start);
        $dateTimeS = self::date3339($str_date, $this->date_offset);

        $start->setDateTime($dateTimeS);
        $event->setStart($start);
        $end = new Google_Service_Calendar_EventDateTime();
        //$dateTimeE = gtime(set_data($date_end), $time_end);
        //$dateTimeE = date(DATE_ATOM, strtotime($date_end . " " . $time_end));
        $str_date = strtotime($date_end . " " . $time_end);
        $dateTimeE = self::date3339($str_date, $this->date_offset);

        $end->setDateTime($dateTimeE);
        $event->setEnd($end);

        $attendee1 = new Google_Service_Calendar_EventAttendee();
        $attendee1->setEmail($email);
        $attendees = array($attendee1);

        $event->attendees = $attendees;
        $createdEvent = $this->service->events->insert($catId, $event);

        return $createdEvent->getId();
    }

//    function addAttachment($calendarService, $driveService, $calendarId, $eventId, $fileId) {
//        $file = $driveService->files->get($fileId);
//        $event = $calendarService->events->get($calendarId, $eventId);
//        $attachments = $event->attachments;
//
//        $attachments[] = array(
//            'fileUrl' => $file->alternateLink,
//            'mimeType' => $file->mimeType,
//            'title' => $file->title
//        );
//        $changes = new Google_Service_Calendar_Event(array(
//            'attachments' => $attachments
//        ));
//
//        $calendarService->events->patch($calendarId, $eventId, $changes, array(
//            'supportsAttachments' => TRUE
//        ));
//    }

    /**
     * delete_event
     * @param type $event_id
     * @return type
     */
    public function delete_event($event_id, $catId = 'primary') {
        //return $this->service->events->delete('primary', $_SESSION['eventID']);
        return $this->service->events->delete($catId, $event_id);
    }

    /**
     * 
     * @param type $params
      array(
      'email' => $email,
      'title' => $title,
      'location' => $location,
      'date_start' => $date_start,
      'time_start' => $time_start,
      'date_end' => $date_end,
      'time_end' => $time_end,
      );
     * @return type
     */
    public function update_event($params) {
        extract($params);

        $catId = isset($params['catId']) && !empty($params['catId']) ? $params['catId'] : "primary";

        $rule = $this->service->events->get($catId, $event_id);

        $event = new Google_Service_Calendar_Event();
        $event->setSummary($title);
        $event->setLocation($location);
        $start = new Google_Service_Calendar_EventDateTime();
        //$dateTimeS = gtime(set_data($date_start), $time_start);
        //$dateTimeS = date(DATE_ATOM, strtotime($date_start . " " . $time_start));
        $str_date = strtotime($date_start . " " . $time_start);
        $dateTimeS = self::date3339($str_date, $this->date_offset);

        $start->setDateTime($dateTimeS);
        $event->setStart($start);
        $end = new Google_Service_Calendar_EventDateTime();
        //$dateTimeE = gtime(set_data($date_end), $time_end);
        //$dateTimeE = date(DATE_ATOM, strtotime($date_end . " " . $time_end));
        $str_date = strtotime($date_end . " " . $time_end);
        $dateTimeE = self::date3339($str_date, $this->date_offset);

        $end->setDateTime($dateTimeE);
        $event->setEnd($end);

        $attendee1 = new Google_Service_Calendar_EventAttendee();
        $attendee1->setEmail($email); //change this
        $attendees = array($attendee1);
        $event->attendees = $attendees;

        $updatedRule = $this->service->events->update($catId, $rule->getId(), $event);
        return $updatedRule;
    }

    /**
     * get_list_event return all the event for primary calendar
     */
    public function get_list_event() {
        if (!$this->is_connected())
            return;

        $ret = array();
        $events = $this->service->events->listEvents($this->google_client_calendar);
        while (true) {
            foreach ($events->getItems() as $event) {
                $ret[$event->getId()] = $event->getSummary();
            }
            $pageToken = $events->getNextPageToken();
            if ($pageToken) {
                $optParams = array('pageToken' => $pageToken);
                $events = $this->service->events->listEvents($this->google_client_calendar, $optParams);
            } else {
                break;
            }
        }
        return $ret;
    }

    /**
     * 
     * @param type $params
     *         $params = array(
      'height' => 600,
      'width' => 800,
      'wkst' => 1,
      'bgcolor' => '#FFFFFF',
      'color' => '#29527A',
      'src' => 'magikboo23@gmail.com',
      'ctz' => 'Europe/Rome'
      );
     */
    public function print_calendar_by_calendar_id($params) {
        $str = "?";
        $i = 0;
        foreach ($params as $k => $v) {
            if ($i <= 0)
                $str .= "$k=" . urlencode($v);
            else
                $str .= "&amp;$k=" . urlencode($v);
            $i++;
        }
        ?>
        <iframe src="https://www.google.com/calendar/embed?<?php echo $str; ?>" style=" border-width:0 " width="800" height="600" frameborder="0" scrolling="no"></iframe>
        <?php
    }

    /**
     * date3339 transform timestamp into google calendar date compliant
     * @param type $timestamp
     * @param type $offset
     * @return string
     */
    public static function date3339($timestamp = 0, $offset = 0) {
        $offset = get_option('gmt_offset')+1;
        sln_my_wp_log("wp offset");
        sln_my_wp_log($offset);
        if (!$timestamp)
            return "error";
        $date = date('Y-m-d\TH:i:s', $timestamp);
        $date .= sprintf(".000%+03d:%02d", intval($offset), abs($offset - intval($offset)) * 60);
        return $date;
    }

    /**
     * create_event_from_booking
     * @param type $booking
     * @return type
     */
    public function create_event_from_booking($booking, $cancel = false) {
        if (!$this->is_connected())
            return;

        $gc_event = new SLN_GoogleCalendarEventFactory();
        $event = $gc_event->get_event($booking);

        if ($cancel)
            $event->setColorId("11");
        else
            $event->setColorId("8");

        $attendee1 = new Google_Service_Calendar_EventAttendee();
        $attendee1->setEmail($this->google_client_calendar);
        $attendees = array($attendee1);

        $event->attendees = $attendees;
        $createdEvent = $this->service->events->insert($this->google_client_calendar, $event);

        return $createdEvent->getId();
    }

    /**
     * create_event_from_booking
     * @param type $booking
     * @return type
     */
    public function update_event_from_booking($booking, $b_event_id, $cancel = false) {
        if (!$this->is_connected())
            return;
        
        $gc_event = new SLN_GoogleCalendarEventFactory();
        $event = $gc_event->get_event($booking);

        if ($cancel)
            $event->setColorId("11");
        else
            $event->setColorId("8");

        $attendee1 = new Google_Service_Calendar_EventAttendee();
        $attendee1->setEmail($this->google_client_calendar); //change this

        $attendees = array($attendee1);
        $event->attendees = $attendees;

        /*$rule = $this->service->events->get($this->google_client_calendar, $b_event_id);*/

        sln_my_wp_log("event updating");
        sln_my_wp_log($event);

        $updatedRule = $this->service->events->update($this->google_client_calendar, $b_event_id, $event);

        $rule = $this->service->events->get($this->google_client_calendar, $b_event_id);
        
        return $updatedRule->getId();
    }

    /**
     * delete_event
     * @param type $event_id
     * @return type
     */
    public function delete_event_from_booking($event_id) {
        //if (!$this->is_connected())
        //  return;
        try {
            $this->service->events->delete($this->google_client_calendar, $event_id);
            sln_my_wp_log($event_id);
            sln_my_wp_log($this->google_client_calendar);
            //$event_id = $GLOBALS['sln_googlescope']->update_event_from_booking($booking, $b_event_id, true);
        } catch (Exception $e) {
            sln_my_wp_log($e);
        }
    }

    public function clear_calendar_events() {
        if (!$this->is_connected())
            return;

        $url = 'https://www.googleapis.com/calendar/v3/calendars/' . $this->google_client_calendar . '/clear';
        sln_my_wp_log($url);
        $ret = wp_remote_get($url);
        sln_my_wp_log($ret);
        return $ret;
    }

}

class SLN_GoogleCalendarEventFactory extends Google_Service_Calendar_Event {

    public function get_event($booking) {
        require_once SLN_PLUGIN_DIR . "/src/SLN/Enum/BookingStatus.php";

        $desc = "";
        //Name and Phone
        $desc .= __('Customer name', 'sln') . ": " . $booking->getDisplayName() . " - ";
        $desc .= $booking->getPhone() . " \n";
        //Services
        $services = $booking->getServices();
        $desc .= "\n" . __('Services booked', 'sln') . ":";
        foreach ($services as $service) {
            $desc .= "\n";
            $desc .= $service->getName();
            $desc .= $service->getContent();
        }
        $notes = $booking->getNote();
        $desc .= "\n\n" . __('Booking notes', 'sln') . ":\n" . (empty($notes) ? __("None", 'sln') : $notes);
        $desc .= "\n\n" . __('Selected assistant', 'sln') . ": " . $booking->getAttendant();
        $desc .= "\n\n" . __('Booking status', 'sln') . ": " . SLN_Enum_BookingStatus::getLabel($booking->getStatus());
        $desc .= "\n\n" . __('Booking URL', 'sln') . ": " . get_permalink($booking->getId());

        $title = $booking->getDisplayName() . " - " . $booking->getStartsAt()->format('d/m/Y h:iA');
        sln_my_wp_log($title);

        $event = new Google_Service_Calendar_Event();
        $event->setSummary($title);
        $event->setDescription($desc);
        $event->setLocation($booking->getAddress());

        $start = new Google_Service_Calendar_EventDateTime();
        $str_date = strtotime($booking->getStartsAt()->formatLocal('Y-m-d H:i:s'));
        $dateTimeS = SLN_GoogleScope::date3339($str_date);
        sln_my_wp_log("start_date");
        sln_my_wp_log($dateTimeS);
        $start->setDateTime($dateTimeS);
        $event->setStart($start);

        $end = new Google_Service_Calendar_EventDateTime();
        $str_date = strtotime($booking->getEndsAt()->formatLocal('Y-m-d H:i:s'));
        $dateTimeE = SLN_GoogleScope::date3339($str_date);
        sln_my_wp_log("end_date");
        sln_my_wp_log($dateTimeE);
        $end->setDateTime($dateTimeE);
        $event->setEnd($end);

        return $event;
    }

}

function synch_a_booking($post_id, $post, $sync = false) {
    if (!$sync)
        remove_action('save_post', 'test_booking', 12, 2);

    // Make sure the post obj is present and complete. If not, bail.
    if (!is_object($post) || !isset($post->post_type)) {
        return;
    }

    sln_my_wp_log("############################################################################");
    //$event->setRecurrence(array('RRULE:FREQ = WEEKLY; UNTIL = 20121231'));
    switch ($post->post_type) { // Do different things based on the post type
        case "sln_booking":
            $booking = new SLN_Wrapper_Booking($post);

            sln_my_wp_log($post);
            sln_my_wp_log($booking);
            sln_my_wp_log($booking->getStartsAt());
            sln_my_wp_log($booking->getEndsAt());

            $event_id = "";
            $b_event_id = get_post_meta($booking->getId(), '_sln_calendar_event_id', true);
            sln_my_wp_log($b_event_id);
            sln_my_wp_log($booking->getStatus());

            require_once SLN_PLUGIN_DIR . "/src/SLN/Enum/BookingStatus.php";
            if ($booking->getStatus() === SLN_Enum_BookingStatus::CANCELED) {
                //If cancelled and i have a event i need to delete it or do return
                if (isset($b_event_id) && !empty($b_event_id)) {
                    sln_my_wp_log("delete");
                    try {
                        $GLOBALS['sln_googlescope']->delete_event_from_booking($b_event_id);
                        update_post_meta($booking->getId(), '_sln_calendar_event_id', '');
                    } catch (Exception $e) {
                        sln_my_wp_log($e);
                        return;
                    }
                } else {
                    sln_my_wp_log("do nothing");
                    return;
                }
            } else {
                if (isset($b_event_id) && !empty($b_event_id)) {
                    sln_my_wp_log("update");
                    try {
                        $event_id = $GLOBALS['sln_googlescope']->update_event_from_booking($booking, $b_event_id, ($booking->getStatus() === SLN_Enum_BookingStatus::CANCELED));
                    } catch (Exception $e) {
                        $b_event_id = "";
                        update_post_meta($booking->getId(), '_sln_calendar_event_id', '');
                    }
                }
                if (!(isset($b_event_id) && !empty($b_event_id))) {
                    sln_my_wp_log("create");
                    try {
                        $event_id = $GLOBALS['sln_googlescope']->create_event_from_booking($booking, ($booking->getStatus() === SLN_Enum_BookingStatus::CANCELED));
                        update_post_meta($booking->getId(), '_sln_calendar_event_id', $event_id);
                    } catch (Exception $e) {
                        sln_my_wp_log($e);
                        return;
                    }
                }
                sln_my_wp_log($event_id);
            }
            break;

        default:
            break;
    }
}

add_action('save_post', 'synch_a_booking', 12, 2);

/*
 * aggiungere in Metabox/Booking.php - getFieldList => '_sln_calendar_event_id' => ''
 */

//SLN_GoogleScope::init();
//echo "-->".SLN_GoogleScope::$outh2_client_id;
//
//$cal_list = SLN_GoogleScope::get_calendar_list();
//
//$params = array(
//    'email' => $cal_list[0],
//    'title' => "mio evento",
//    'location' => "fiumicino",
//    'date_start' => "20-09-2015",
//    'time_start' => "20:00",
//    'date_end' => "21-09-2015",
//    'time_end' => "20:00",
//);
//$i = SLN_GoogleScope::create_event($params);
////SLN_GoogleScope::delete_event($i);
//
//$cparams = array(
//    'height' => 600,
//    'width' => 800,
//    'wkst' => 1,
//    'bgcolor' => '#FFFFFF',
//    'color' => '#29527A',
//    'src' => $cal_list[0],
//    'ctz' => 'Europe/Rome'
//);
//SLN_GoogleScope::print_calendar_by_calendar_id($cparams);
//SLN_GoogleScope::get_list_event($cal_list[0]);
//add_action('wp_loaded', function() {
//    if (isset($_GET['code']))
//        do_action('onchangeapi', SLN_GoogleScope::init());
//});

class SLN_Bookings_Handle {

    private $from;

    public function __construct($from) {
        $this->from = $from;
    }

    public function getBookings() {
        return $this->getResults();
    }

    private function getResults() {
        $bookings = $this->buildBookings();
        $ret = array();
        foreach ($bookings as $b) {
            $ret[] = $b;
        }
        return $ret;
    }

    private function buildBookings() {
        $args = array(
            'post_type' => SLN_Plugin::POST_TYPE_BOOKING,
            'nopaging' => true,
            'meta_query' => $this->getCriteria()
        );
        $query = new WP_Query($args);
        $ret = array();
        foreach ($query->get_posts() as $p) {
            $ret[] = $p;
        }
        wp_reset_query();
        wp_reset_postdata();

        return $ret;
    }

    public function createBooking($booking) {
        if (is_int($booking)) {
            $booking = get_post($booking);
        }

        return new SLN_Wrapper_Booking($booking);
    }

    private function getCriteria() {
        $from = $this->from->format('Y-m-d');
        $criteria = array(
            array(
                'key' => '_sln_booking_date',
                'value' => $from,
                'compare' => '>=',
            )
        );
        return $criteria;
    }

    private function getTitle($booking) {
        return $booking->getTitle();
    }

    private function getEventHtml($booking) {
        return $booking->getDisplayName();
    }

}
?>
