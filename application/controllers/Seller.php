<?php
include_once(APPPATH.'libraries/REST_Controller.php');
defined('BASEPATH') OR exit('No direct script access allowed');

class seller extends REST_Controller {
    
    
        function SellerLogin_post(){
            $data = array('email' =>$this->post('email'));
            $this->db->select('`password`');
            $this->db->from('mt_merchant');
            $this->db->where($data);
            $row = $this->db->get()->row();

        
            if($row){
                
              
                
    
                if($this->callSeller($this->post('email'),$this->post('password'),$row->password) == false){
                    
                        $this->response(array('status' => 'fail','message' => null, 'code' => 502));
                }else{
                    
                       $this->response(array('status'=>'success','message'=>$this->callSeller($this->post('email'),$this->post('password'),$row->password), 'code' => 200));
                       
                }
                
                
             
            }else{
                $this->response(array('status' => 'fail','message' => null, 'code' => 502));
            }
        }
        
        
        function SellerRegister_post(){
            
            $merchant_id = $this->getMaxIdMerchant();
            $email = $this->post('email');
            $username = $this->post('username');
            $password = $this->post('password');
            $merchant_name = $this->post('merchant_name');
            $merchant_phone = $this->post('merchant_phone');
            
            
            if($this->checkEmailAlready($email) == false){
                
                        $options = [
                        'cost' => 12
                        ];
                        
                        $data = array(
                        'merchant_id' =>$merchant_id,
                        'username' => $username,
                        'email' => $email,
                        'status' => 'active',
                        'status_merchant' => '1',
                        'merchant_name' => $merchant_name,
                        'merchant_phone' => $merchant_phone,
                        'password' => password_hash($password, PASSWORD_BCRYPT, $options),
                            );
                            
                    $myInsert = $this->db->insert('mt_merchant', $data);
               
                    if($myInsert){
                        $this->response(array('status'=>'success','message'=>'register sukses, silahkan periksa email', 'code' => 200));
                    }else{
                        $this->response(array('status' => 'fail','message' => null, 'code' => 502));
                    }
                
                
            }else{
               
                
                 $this->response(array('status' => 'fail','message' => $this->checkEmailAlready($email), 'code' => 502));
                 
            }
        }
        
        
        function callSeller($email, $password, $password_hash){
            if (password_verify($password,$password_hash)) {
                
                $data = array('email' => $email, 'password' => $password_hash);
                
                        $this->db->select('*');
                        $this->db->from('mt_merchant');
                        $this->db->where($data);
                        
                      return  $customer = $this->db->get()->result();
                } else {
                    return false;
            }
        }
        
        
           function getMaxIdMerchant(){
            $this->db->select_max('merchant_id');
            $this->db->from('mt_merchant');
            $row = $this->db->get()->row();
            return $row->merchant_id+1;
        }
        
        function checkEmailAlready($email){
            error_reporting(0);
            $data = array('email' =>$email);
            $this->db->select('`email`');
            $this->db->from('mt_merchant');
            $this->db->where($data);
            $row = $this->db->get()->row();
            
            if($row->email == $email){
                return true;
            }else{
                return false;
            }
        }
        
       

}

?>