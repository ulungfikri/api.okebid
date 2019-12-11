<?php
include_once(APPPATH.'libraries/REST_Controller.php');
defined('BASEPATH') OR exit('No direct script access allowed');

class example extends REST_Controller {

    function index_get() {
         $this->response(array('status'=>'success','message'=>null, 'code' => 200));
    }
}

?>