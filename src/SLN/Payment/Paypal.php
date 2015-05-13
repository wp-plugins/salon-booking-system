<?php

class SLN_Payment_Paypal
{
    const TEST_URL = 'https://sandbox.paypal.com/cgi-bin/webscr';
    const PROD_URL = 'https://www.paypal.com/cgi-bin/webscr';
    protected $plugin;

    public function __construct(SLN_Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    function reverseCheckIpn()
    {
        $raw_post_data  = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost         = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2) {
                $myPost[$keyval[0]] = urldecode($keyval[1]);
            }
        }

        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }
        $isTest = $this->plugin->getSettings()->isPaypalTest();
        

        $ch = curl_init($this->getBaseUrl());
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $isTest ? 0 : 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $isTest ? 0 : 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

        if (!($res = curl_exec($ch))) {
            error_log("Got " . curl_error($ch) . " when processing IPN data");
            curl_close($ch);
            exit;
        }
        curl_close($ch);


        return (strcmp($res, "VERIFIED") == 0);
    }


    public function getUrl($id, $amount, $title)
    {
        $settings = $this->plugin->getSettings();
        $url      = SLN_Func::currPageUrl();

        return $this->getBaseUrl() . "?"
        . http_build_query(
            array(
                'notify_url'    => add_query_arg('op', 'notify-' . $id, $url),
                'return'        => add_query_arg('op', 'success-' . $id, $url),
                'cancel_return' => add_query_arg('op', 'cancel-' . $id, $url),
                'cmd'           => '_xclick',
                'business'      => $settings->getPaypalEmail(),
                'currency_code' => $settings->getCurrency(),
                'amount'        => $amount,
                'item_name'     => $title
            )
        );
    }

    private function getBaseUrl()
    {
        $isTest = $this->plugin->getSettings()->isPaypalTest();
        return $isTest ?
            self::TEST_URL : self::PROD_URL;
    }

    function isCompleted($amount)
    {
        return floatval($_POST['mc_gross']) == floatval($amount) && $_POST['payment_status'] == 'Completed';
    }
    function getTransactionId(){
        return $_POST['txn_id'];
    }
}
