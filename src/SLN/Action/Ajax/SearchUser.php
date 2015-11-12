<?php

class SLN_Action_Ajax_SearchUser extends SLN_Action_Ajax_Abstract
{
    public function execute()
    {
       if(!current_user_can( 'manage_options' )) throw new Exception('now allowed');
       $result = $this->getResult($_GET['s']);
       if(!$result){
           $ret = array(
               'success' => 0,
               'errors' => array(__('User not found','sln'))
           );
       }else{
           $ret = array(
               'success' => 1,
               'result' => $result,
               'message' => __('User updated','sln')
           );
       }
       return $ret;
    }
    private function getResult($search)
    {
        $include = $this->userSearch($search);
        $number = 10;
        if(!$include)
            $user_query = new WP_User_Query( compact('search', 'number') );
        else
            $user_query = new WP_User_Query( compact('include', 'number') );

        if(!$user_query->results) return [];
        else $results = $user_query->results;

        $value = array();
        foreach($results as $u){
            $values[] = array(
                'id' => $u->ID,
                'text' => $u->user_firstname.' '.$u->user_lastname.' ('.$u->user_email.')',
            );
        }
        return $values;
    }

    public function userSearch($wp_user_query) {
            global $wpdb;

            $uids=array();			

			$flsiwa_add = "";
			// Escaped query string
			$qstr = addslashes($_GET["s"]);
			if(preg_match('/\s/',$qstr)){
				$pieces = explode(" ", $qstr);
				$user_ids_collector = $wpdb->get_results("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE (meta_key='first_name' AND LOWER(meta_value) LIKE '%".$pieces[0]."%')");

	            foreach($user_ids_collector as $maf) {
	                if(strtolower(get_user_meta($maf->user_id, 'last_name', true)) == strtolower($pieces[1])){
						array_push($uids,$maf->user_id);
	                }
	            }

			}else{				

				$user_ids_collector = $wpdb->get_results("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE (meta_key='first_name' OR meta_key='last_name'".$flsiwa_add.") AND LOWER(meta_value) LIKE '%".$qstr."%'");
					foreach($user_ids_collector as $maf) {
	                array_push($uids,$maf->user_id);
	            }
			}

            $users_ids_collector = $wpdb->get_results("SELECT DISTINCT ID FROM $wpdb->users WHERE LOWER(user_nicename) LIKE '%".$qstr."%' OR LOWER(user_email) LIKE '%".$qstr."%'");
            foreach($users_ids_collector as $maf) {
                if(!in_array($maf->ID,$uids)) {
                    array_push($uids,$maf->ID);
                }
            }
        return $uids;
    }
}
