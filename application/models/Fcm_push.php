<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @모델기능: Push모델
 * @상세설명:
 * @작성자:
 * @최초작성일:
 * @최종수정일: -
 */
class Fcm_push extends CI_Model {


   protected $api_key;



   function __construct()
    {
      parent::__construct();

      $app_key_path = $this->config->item('app_key_path');
      $trimmed = file($app_key_path. 'app_push.key', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      $this->api_key = trim($trimmed[0]);

    
    }

   // send method for KMH SET
   public function app_push($mb_id, $title, $message='', $url='') {
      $data = array();
      $mb_data = $this->db->select('*')->get_where('member', array('mb_id' => $mb_id))->row_array();

      if($mb_data['mb_app_token']){
         $title = str_replace(';', ' ', $title);
         $message = str_replace(';', ' ', $message);
         $url = base_url($url);

         $data['post_key'] = 'title;message;url';
         $data['post_value'] = "{$title};{$message};{$url}";
         $data['mb_app_token'] = $mb_data['mb_app_token'];
         $data['mb_app_os'] = $mb_data['mb_app_os'];
         return $result = $this->push_send($data);
      } else {
         return 'fail : no mb_app_token';
      }
   }

   // TEST
   public function push_test($mb_id){
      $data = array();
      $mb_data = $this->db->select('*')->get_where('member', array('mb_id' => $mb_id))->row_array();

      if($mb_data['mb_app_token']){
         $data['post_key'] = 'title;message;url';
         $data['post_value'] = '푸쉬제목;푸쉬테스트;'. base_url('/coupon');
         $data['mb_app_token'] = $mb_data['mb_app_token'];
         $data['mb_app_os'] = $mb_data['mb_app_os'];
         return $result = $this->push_send($data);
      } else {
         return 'fail : no mb_app_token';
      }
   }


   /**
    * fcm PUSH 전송
    * @param $post_data
    */
   public function push_send(&$post_data){

      $data = array();
      $result = array();

      $key_array = explode(";", $post_data['post_key']);
      $value_array = explode(";", $post_data['post_value']);

      for($i=0; $i<count($key_array); $i++)
      {

         $data[trim($key_array[$i])] = $value_array[$i];

      }

      $url = 'https://fcm.googleapis.com/fcm/send';
      $headers = array(
                     'Authorization: key='.$this->api_key,
                     'Content-Type: application/json'
                  );

      if(strtolower($post_data['mb_app_os']) == 'android'){
         $fields = array (
               'data' => $data,
               'to' => $post_data['mb_app_token'],
               'priority' => 'high'
            );
      }
      elseif(strtolower($post_data['mb_app_os']) == 'ios'){
         $data['body'] =  $data['message'];
         $data['sound'] = 'default';
         $data['badge'] = '0';

         $fields = array (
               'data' => $data,
               'notification' => $data, //안드로이드에 함께 보낼 경우 FirebaseMessagingService call 되지 않음.
               'to' => $post_data['mb_app_token'],
               'priority' => 'high'

            );
      }


      $fields['to'] = $post_data['mb_app_token'];
      $fields['priority'] = "high";

      // kmh_print($url);
      // kmh_print($headers);
      // kmh_print($fields);

      $ch = curl_init();
      @curl_setopt( $ch, CURLOPT_URL, $url );
      @curl_setopt( $ch, CURLOPT_POST, true );
      @curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
      @curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
      //@curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
      @curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode ($fields) );
      $curl_result = @curl_exec( $ch );

      if(curl_errno( $ch )){

         $result['response']  = curl_error( $ch );
      }
      else{

         $jsonObj = json_decode($curl_result,true);

         if($jsonObj['success']=='1'){
               $result['status'] = 'success';
               $result['response'] = $curl_result;

         }

         else{

            $result['status'] = 'fail';
            $result['response'] = $curl_result;
         }

      }


      return $result;
   }

}
