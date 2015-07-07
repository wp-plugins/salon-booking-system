<?php

class SLN_Action_Ajax_SalonStep extends SLN_Action_Ajax_Abstract
{
    public function execute()
    {
        if (isset($_POST['sln_step_page'])) {
            $_GET['sln_step_page'] = $_POST['sln_step_page'];
        }
        if (isset($_POST['mode'])) {
            $_GET['mode'] = $_POST['mode'];
        }

        try {
            $ret = do_shortcode('[' . SLN_Shortcode_Salon::NAME . '][/' . SLN_Shortcode_Salon::NAME . ']');
            $ret = array(
                'content' => $ret,
                'nonce' => wp_create_nonce('ajax_post_validation')
            );
        } catch (SLN_Action_Ajax_RedirectException $ex) {
            $ret = array(
                'redirect' => $ex->getMessage()
            );
        }
        return $ret;
    }
}
