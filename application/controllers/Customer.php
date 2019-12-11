<?php
include_once(APPPATH.'libraries/REST_Controller.php');
defined('BASEPATH') OR exit('No direct script access allowed');

class customer extends REST_Controller {
    
    function CustomerLogin_post(){
        $data = array('email' =>$this->post('email'));
        $this->db->select('`password`');
        $this->db->from('mt_client');
        $this->db->where($data);
        $row = $this->db->get()->row();

        
        if($row){
            
            if($this->callCustomer($this->post('email'),$this->post('password'),$row->password) == false){
                
                $this->response(array('status' => 'fail','message' => null, 'code' => 502));
                
            }else{
                $this->response(array('status'=>'success','message'=>$this->callCustomer($this->post('email'),$this->post('password'),$row->password), 'code' => 200));
            }
            
            
        }else{
            $this->response(array('status' => 'fail','message' => null, 'code' => 502));
        }
    }
    
    

    function CustomerRegister_post(){
        
        $email = $this->post('email');
        $username = $this->post('username');
        $password = $this->post('password');
        
        
        if($this->checkEmailAlready($email) == false){
            
            $options = [
                'cost' => 12
            ];
            
            $data = array(
                'social_strategy' => 'mobile',
                'email' => $email,
                'status' => 'active',
                'password' => password_hash($password, PASSWORD_BCRYPT, $options),
            );
            
            $myInsert = $this->db->insert('mt_client', $data);
            
            if($myInsert){
                $this->response(array('status'=>'success','message'=>'register sukses, silahkan periksa email', 'code' => 200));
            }else{
                $this->response(array('status' => 'fail','message' => null, 'code' => 502));
            }
            
            
        }else{
         
            
           $this->response(array('status' => 'fail','message' => 'email sudah tersedia, silahkan dengan email lain', 'code' => 502));
           
       }
   }
   
   


   function callCustomer($email, $password, $password_hash){
    if (password_verify($password,$password_hash)) {
        
        $data = array('email' => $email, 'password' => $password_hash);
        
        $this->db->select('*');
        $this->db->from('mt_client');
        $this->db->where($data);
        
        return  $customer = $this->db->get()->result();
    } else {
        return false;
    }
}

    function checkEmailAlready($email){
        error_reporting(0);
        $data = array('email' =>$email);
        $this->db->select('`email`');
        $this->db->from('mt_client');
        $this->db->where($data);
        $row = $this->db->get()->row();
        
        if($row->email == $email){
            return true;
        }else{
            return false;
    }
}


    function Message_post(){
        
        $id = $this->getMaxIdMessage();
        $data = array(
            'message_id' => $id,
            'client_id' => $this->post('client_id'),
            'merchant_id' => $this->post('merchant_id'),
            'created_at' => $this->post('created_at'));
        
        $myInsert = $this->db->insert('message', $data);
        
        if($myInsert){
            if($this->MessageDetail($id, $this->post('client_id'),$this->post('merchant_id'), $this->post('content'), $this->post('created_at'))==true){
                
               $this->response(array('status'=>'success','message'=>'sukses kirim pesan', 'code' => 200));
               
           }else{
               $this->response(array('status'=>'failed','message'=>null, 'code' => 202));
           }
       }else{
         $this->response(array('status'=>'failed','message'=>null, 'code' => 202)); 
    }
}


    function getMaxIdMessage(){
        $this->db->select_max('message_id');
        $this->db->from('message');
        $row = $this->db->get()->row();
        return $row->message_id+1;
    }


    function MessageDetail($message_id, $client_id, $merchant_id, $content, $created_at){

    $data = array(
    'message_id' => $message_id,
    'client_id' => $client_id,
    'merchant_id' => $merchant_id,
    'content' => $content,
    'created_at' => $created_at,
    );

    $myInsert = $this->db->insert('message_detail', $data);

    if($myInsert){
        return true;
    }else{
        return false;
    }
}


function GetMessage_post (){
    
    
    $data = array('A.client_id' =>$this->post('client_id'));
    
    $this->db->select('
        A.`message_id`, A.`client_id`, A.`merchant_id`, A.`created_at`, A.`updated_at`, A.`deleted_at`,
        B.id as messagedetail_id, B.content
        ');
    
    $this->db->from('message A');
    $this->db->join('message_detail B', 'A.`message_id` = B.message_id', 'right');
    $this->db->where($data);
    $myData = $this->db->get()->result();
    
    if($myData){
        $this->response(array('status'=>'success','message'=>$myData, 'code' => 200));
    }else{
        $this->response(array('status'=>'failed','message'=>null, 'code' => 202));
    }
}



function UpdateProfil_post(){
    
    
    $client_id = $this->post('idClient');
    $first_name = $this->post('firstName');
    $last_name = $this->post('lastName');
    $email = $this->post('email');
    $street = $this->post('alamat');
    $city = $this->post('kota');
    $zipcode = $this->post('kodePos');
    
    
    if($this->checkEmailAlready($email) == false){
        
     
     $data = array(
         'first_name' => $first_name,
         'last_name' => $last_name,
         'email' => $email,
         'street' => $street,
         'city' => $city,
         'zipcode' => $zipcode
     );

     $this->db->where('client_id', $client_id);
     $myInsert = $this->db->update('mt_client', $data);
     if($myInsert){
        
        $datas = array('client_id' => $client_id);
        
        $this->db->select('*');
        $this->db->from('mt_client');
        $this->db->where($datas);
        
        $customer = $this->db->get()->result();
        
        
        
        $this->response(array('status'=>'success','message'=>$customer, 'code' => 200));
        
    }else{
        $this->response(array('status' => 'fail','message' => null, 'code' => 502));
    }
    
    
}else{
 
   $this->response(array('status' => 'email sudah tersedia, silahkan dengan email lain','message' => NULL, 'code' => 502));
   
}
}



function UpdateBank_post(){
       
    $idClient = $this->post('idClient');
    $fullname = $this->post('fullname');
    $bankName = $this->post('bankName');
    $typeAkunBank = $this->post('typeAkunBank');
    $noRekening = $this->post('noRekening');
    $KodeCvv = $this->post('KodeCvv');

    
    
    
    
    $data = array(
     'bank' => $bankName,
     'norek' => $noRekening,
     'namarek' => $fullname
 );

    $this->db->where('client_id', $idClient);
    $myInsert = $this->db->update('mt_client', $data);
    
    if($myInsert){
        
        $datas = array('client_id' => $idClient);
        
        $this->db->select('*');
        $this->db->from('mt_client');
        $this->db->where($datas);
        
        $customer = $this->db->get()->result();
        
        
        
        $this->response(array('status'=>'success','message'=>$customer, 'code' => 200));
        
    }else{
        $this->response(array('status' => 'fail','message' => null, 'code' => 502));
    }
    
    
}



function Alamat_post(){
   
    $client_id = $this->post('client_id');
    $datas = array('client_id' => $client_id);
    $this->db->select('*');
    $this->db->from('mt_address_book');
    $this->db->where($datas);
    $customer = $this->db->get()->result();
    
    if($customer){
        $this->response(array('status'=>'success','message'=>$customer, 'code' => 200));
    }else{
        $this->response(array('status'=>'failed','message'=>NULL, 'code' => 202));
    }
}



function UpdateAlamat_post(){
    
    $idAlamat = $this->post('idAlamat');
    $idClient = $this->post('idClient');
    $street = $this->post('street');
    $city = $this->post('city');
    $state = $this->post('state');
    $zipcode = $this->post('zipcode');
    $location_name = $this->post('location_name');
    $lat = $this->post('lat'); 
    $long = $this->post('longs');

    
    $data = array(
     'street' => $street,
     'city' => $city,
     'state' => $state,
     'zipcode' => $zipcode,
     'location_name' => $location_name,
     'lat' => $lat,
     'long' => $long
 );
    
    

    $dataWhere = array(
     'id' => $idAlamat,
     'client_id' => $idClient
 );

    $this->db->where($dataWhere);
    
    $myInsert = $this->db->update('mt_address_book', $data);
    
    if($myInsert){
        $this->response(array('status'=>'success','message'=>NULL, 'code' => 200));
    }else{
        $this->response(array('status' => 'fail','message' => null, 'code' => 202));
    }
}



function TambahAlamat_post(){
    
    $idClient = $this->post('idClient');
    $street = $this->post('street');
    $city = $this->post('city');
    $state = $this->post('state');
    $zipcode = $this->post('zipcode');
    $location_name = $this->post('location_name');
    $lat = $this->post('lat'); 
    $long = $this->post('longs');

    
    $data = array(
     'client_id' => $idClient,
     'street' => $street,
     'city' => $city,
     'state' => $state,
     'zipcode' => $zipcode,
     'location_name' => $location_name,
     'lat' => $lat,
     'long' => $long
 );
    
    

    $myInsert = $this->db->insert('mt_address_book', $data);
    
    if($myInsert){
        $this->response(array('status'=>'success','message'=>NULL, 'code' => 200));
    }else{
        $this->response(array('status' => 'fail','message' => null, 'code' => 502));
    }
}


function RemoveAlamat_post(){
    
    $idClient = $this->post('idClient');
    $idAlamat = $this->post('idAlamat');


    
    $data = array(
     'id' => $idAlamat,
     'client_id' => $idClient
 );
    
    
    $this->db->where($data);
    $myDelete =  $this->db->delete('mt_address_book');
    
    if($myDelete){
        $this->response(array('status'=>'success','message'=>NULL, 'code' => 200));
    }else{
        $this->response(array('status' => 'fail','message' => null, 'code' => 502));
    }
}



        //  function UpdateAlamat_post(){

        //     $idAlamat = $this->post('idAlamat');
        //     $idClient = $this->post('idClient');
        //     $street = $this->post('street');
        //     $city = $this->post('city');
        //     $state = $this->post('state');
        //     $zipcode = $this->post('zipcode');
        //     $location_name = $this->post('location_name');
        //     $lat = $this->post('lat'); 
        //     $long = $this->post('longs');


        //               $data = array(
        //                   'street' => $street,
        //                   'city' => $city,
        //                   'state' => $state,
        //                   'zipcode' => $zipcode,
        //                   'location_name' => $location_name,
        //                   'lat' => $lat,
        //                   'long' => $long
        //                   );



        //             $dataWhere = array(
        //                   'id' => $idAlamat,
        //                   'client_id' => $idClient
        //                   );

        //              $this->db->where($dataWhere);

        //              $myInsert = $this->db->update('mt_address_book', $data);

        //             if($myInsert){
        //                 $this->response(array('status'=>'success','message'=>NULL, 'code' => 200));
        //             }else{
        //                 $this->response(array('status' => 'fail','message' => null, 'code' => 202));
        //             }
        // }

}

?>