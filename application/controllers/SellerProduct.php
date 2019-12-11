<?php
include_once(APPPATH.'libraries/REST_Controller.php');
defined('BASEPATH') OR exit('No direct script access allowed');

class SellerProduct extends REST_Controller {
    
    
    
    function UlasanSeller_post(){
            
    $data = array('A.merchant_id' =>$this->post('merchant_id'));
    $this->db->select('A.*, D.first_name, D.last_name, D.email, D.avatar, D.street, D.city, D.contact_phone,
C.item_name, C.photo, C.price, C.sold, C.sold ');
                        
    $this->db->from('mt_review AS A');
    $this->db->join('mt_merchant AS B ', 'A.merchant_id = B.merchant_id','LEFT');
    $this->db->join('mt_item AS C ', 'A.item_id = C.item_id','LEFT');
    $this->db->join('mt_client AS D ', 'A.client_id = D.client_id','LEFT');
    
    $this->db->where($data);
    $Ulasan = $this->db->get()->result();
            
            if($Ulasan){
                $this->response(array('status'=>'success','message'=>$Ulasan, 'code' => 200));
            }else{
                 $this->response(array('status' => 'fail','message' => null, 'code' => 502));
            }
            
        }
    
    

    function DiskusiSeller_post(){
            
    $data = array('A.merchant_id' =>$this->post('merchant_id'));
    $this->db->select('A.*, C.first_name, C.last_name, C.avatar');
    $this->db->from('message AS A');
    $this->db->join('mt_client AS C ', 'A.client_id = C.client_id','LEFT');
    $this->db->where($data);
    $this->db->order_by('A.merchant_id', 'asc'); 
    $Diskusi = $this->db->get()->result();
            
            if($Diskusi){
                $this->response(array('status'=>'success','message'=>$Diskusi, 'code' => 200));
            }else{
                 $this->response(array('status' => 'fail','message' => null, 'code' => 502));
            }
            
        }
    

     function DiskusiDetailSeller_post(){
            
    $data = array('A.message_id' =>$this->post('message_id'));
    $this->db->select('A.*, B.last_name, B.last_name, B.email, B.avatar');
    $this->db->from('message_detail AS A');
    $this->db->join('mt_client AS B ', 'A.client_id = B.client_id','LEFT');
    $this->db->where($data);
    $DiskusiDetail = $this->db->get()->result();
            
            if($DiskusiDetail){
                
                $this->response(array('status'=>'success','message'=>$DiskusiDetail, 'code' => 200));
                
            }else{
                
                 $this->response(array('status' => 'fail','message' => null, 'code' => 502));
                 
            }
            
        }
    
    
        function AllLelang_post(){
            
            $data = array('C.merchant_id' =>$this->post('merchant_id'));
            
            $this->db->select('A.*, B.item_name, B.photo , C.merchant_id, C.merchant_name, C.merchant_phone');
                        
            $this->db->from('mt_auctions AS A');
            $this->db->join('mt_item AS B ', 'A.item_id = B.item_id','inner');
            $this->db->join('mt_merchant AS C ', 'B.merchant_id = C.merchant_id','inner');
            $this->db->where($data);
            $alllelang = $this->db->get()->result();
            
            if($alllelang){
                $this->response(array('status'=>'success','message'=>$alllelang, 'code' => 200));
            }else{
                 $this->response(array('status' => 'fail','message' => null, 'code' => 502));
            }
            
        }
        
        
          function AllProduct_post(){
            
            $data = array('A.merchant_id' =>$this->post('merchant_id'));
            
            $this->db->select('A.*');
                        
            $this->db->from('mt_item AS A');
            $this->db->where($data);
            $allProduct = $this->db->get()->result();
            
            if($allProduct){
                $this->response(array('status'=>'success','message'=>$allProduct, 'code' => 200));
            }else{
                 $this->response(array('status' => 'fail','message' => null, 'code' => 502));
            }
            
        }
    
    
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