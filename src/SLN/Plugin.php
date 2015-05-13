<?php

class SLN_Plugin
{
    const POST_TYPE_SERVICE = 'sln_service';
    const POST_TYPE_ATTENDANT = 'sln_attendant';
    const POST_TYPE_BOOKING = 'sln_booking';
    const USER_ROLE_STAFF = 'sln_staff';
    const TEXT_DOMAIN = 'sln';
    const F = 'slnc';
    const F1 = 50;
    const F2 = 40;

    private static $instance;
    private $settings;
    private $services;
    private $attendants;
    private $formatter;
    private $availabilityHelper;


    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->init();
        if (is_admin()) {
            $this->initAdmin();
        }
    }

    private function init()
    {
        add_action('init', array($this, 'action_init'));
        add_action('admin_init', array($this, 'add_admin_caps'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        register_activation_hook(SLN_PLUGIN_BASENAME, array('SLN_Action_Install', 'execute'));
        new SLN_PostType_Attendant($this, self::POST_TYPE_ATTENDANT);
        new SLN_PostType_Service($this, self::POST_TYPE_SERVICE);
        new SLN_PostType_Booking($this, self::POST_TYPE_BOOKING);
    }

    private function initAdmin()
    {
        new SLN_Metabox_Service($this, self::POST_TYPE_SERVICE);
        new SLN_Metabox_Attendant($this, self::POST_TYPE_ATTENDANT);
        new SLN_Metabox_Booking($this, self::POST_TYPE_BOOKING);
        new SLN_Admin_Settings($this);
        new SLN_Admin_Calendar($this);
        add_action('admin_notices', array($this, 'admin_notices'));
        //http://codex.wordpress.org/AJAX_in_Plugins
        add_action('wp_ajax_salon', array($this, 'ajax'));
        add_action('wp_ajax_nopriv_salon', array($this, 'ajax'));
        add_action('wp_ajax_saloncalendar', array($this, 'ajax'));

    }

    public function add_admin_caps()
    {
        $role = get_role('administrator');
        $role->add_cap('manage_salon');
    }

    public function action_init()
    {
        if (!session_id()) {
            session_start();
        }
        load_plugin_textdomain(self::TEXT_DOMAIN, false, dirname(SLN_PLUGIN_BASENAME) . '/languages');
        wp_enqueue_style('salon', SLN_PLUGIN_URL . '/css/salon.css', array(), SLN_VERSION, 'all');
        //        wp_enqueue_style('bootstrap', SLN_PLUGIN_URL . '/css/bootstrap.min.css', array(), SLN_VERSION, 'all');
        wp_enqueue_script('smalot-datepicker', SLN_PLUGIN_URL . '/js/bootstrap-datetimepicker.js', array('jquery'), '20140711', true);
        wp_enqueue_script('salon', SLN_PLUGIN_URL . '/js/salon.js', array('jquery'), '20140711', true);
        wp_localize_script(
            'salon',
            'salon',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'ajax_nonce' => wp_create_nonce('ajax_post_validation'),
                'loading' => 'http://i.stack.imgur.com/drgpu.gif'
            )
        );
        SLN_Shortcode_Salon::init($this);
    }

    public function admin_enqueue_scripts()
    {
        wp_enqueue_script('salon-admin-js', SLN_PLUGIN_URL . '/js/admin.js', array('jquery'), '20140711', true);
        wp_enqueue_style('salon-admin-css', SLN_PLUGIN_URL . '/css/admin.css', array(), SLN_VERSION, 'all');
    }

    /** @return SLN_Settings */
    public function getSettings()
    {
        if (!isset($this->settings)) {
            $this->settings = new SLN_Settings();
        }

        return $this->settings;
    }

    public function createAttendant($attendant)
    {
        if (is_int($attendant)) {
            $service = get_post($attendant);
        }

        return new SLN_Wrapper_Attendant($attendant);
    }

    public function createService($service)
    {
        if (is_int($service)) {
            $service = get_post($service);
        }

        return new SLN_Wrapper_Service($service);
    }

    public function createBooking($booking)
    {
        if (is_int($booking)) {
            $booking = get_post($booking);
        }

        return new SLN_Wrapper_Booking($booking);
    }

    public function getBookingBuilder()
    {
        return new SLN_Wrapper_Booking_Builder($this);
    }

    /**
     * @return SLN_Wrapper_Service[]
     */
    public function getServices()
    {
        if (!isset($this->services)) {
            $query = new WP_Query(
                array(
                    'post_type' => self::POST_TYPE_SERVICE,
                    'nopaging' => true
                )
            );
            $ret = array();
            foreach ($query->get_posts() as $p) {
                $ret[] = $this->createService($p);
            }
            wp_reset_query();
            wp_reset_postdata();
            $this->services = $ret;
        }

        return $this->services;
    }

    /**
     * @return SLN_Wrapper_Attendant[]
     */
    public function getAttendants()
    {
        if (!isset($this->attendants)) {
            $query = new WP_Query(
                array(
                    'post_type' => self::POST_TYPE_ATTENDANT,
                    'nopaging' => true
                )
            );
            $ret = array();
            foreach ($query->get_posts() as $p) {
                $ret[] = $this->createAttendant($p);
            }
            wp_reset_query();
            wp_reset_postdata();
            $this->attendants = $ret;
        }

        return $this->attendants;
    }


    public function admin_notices()
    {
        if (current_user_can('install_plugins')) {
            if (isset($_GET['sln-dismiss']) && $_GET['sln-dismiss'] == 'dismiss_admin_notices') {
                $this->getSettings()
                    ->setNoticesDisabled(true)
                    ->save();
            }
            if (!$this->getSettings()->getNoticesDisabled()) {
                $dismissUrl = add_query_arg(array('sln-dismiss' => 'dismiss_admin_notices'));
                echo $this->loadView('admin_notices', compact('dismissUrl'));
            }
            $cnt = get_option(SLN_PLUGIN::F);
            if($cnt>self::F1){
                echo $this->loadView('trial/admin_end');
            }elseif($cnt > self::F2){
                echo $this->loadView('trial/admin_near');
            }
        }
    }

    public function getTextDomain()
    {
        return self::TEXT_DOMAIN;
    }

    public function getViewFile($view)
    {
        return SLN_PLUGIN_DIR . '/views/' . $view . '.php';
    }

    public function loadView($view, $data = array())
    {
        ob_start();
        extract($data);
        $plugin = $this;
        include $this->getViewFile($view);

        return ob_get_clean();
    }

    public function sendMail($view, $data)
    {
        $data['data'] = $settings = new ArrayObject($data);
        $content = $this->loadView($view, $data);
        if (!function_exists('sln_html_content_type')) {
            function sln_html_content_type()
            {
                return 'text/html';
            }
        }
        add_filter('wp_mail_content_type', 'sln_html_content_type');
        wp_mail($settings['to'], $settings['subject'], $content);
        remove_filter('wp_mail_content_type', 'sln_html_content_type');
    }

    /**
     * @return SLN_Formatter
     */
    public function format()
    {
        if (!isset($this->formatter)) {
            $this->formatter = new SLN_Formatter($this);
        }

        return $this->formatter;
    }

    public function getAvailabilityHelper()
    {
        if (!isset($this->availabilityHelper)) {
            $this->availabilityHelper = new SLN_Helper_Availability($this->getSettings());
        }

        return $this->availabilityHelper;
    }

    /**
     * @param Datetime $datetime
     * @return \SLN_Helper_Intervals
     */
    public function getIntervals(Datetime $datetime)
    {
        $obj = new SLN_Helper_Intervals($this->getAvailabilityHelper());
        $obj->setDatetime($datetime);

        return $obj;
    }

    public function ajax()
    {
        check_ajax_referer('ajax_post_validation', 'security');
        $method = $_REQUEST['method'];
        $className = 'SLN_Action_Ajax_' . ucwords($method);
        if (class_exists($className)) {
            /** @var SLN_Action_Ajax_Abstract $obj */
            $obj = new $className($this);
            $ret = $obj->execute();
            if (is_array($ret)) {
                header('Content-Type: application/json');
                echo json_encode($ret);
            } elseif (is_string($ret)) {
                echo $ret;
            }
            exit();
        } else {
            throw new Exception("ajax method not found '$method'");
        }
    }
}
