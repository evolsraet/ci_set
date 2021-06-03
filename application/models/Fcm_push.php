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

   public function app_push_admin($title, $message='', $url='') {
      $this->member_model
         ->where('mb_level >=', 90)
         ->where('mb_id !=', 1);
      foreach( (array) $this->member_model->get_all() as $key => $row ) :
         $this->app_push($row->mb_id, $title, $message, $url);
      endforeach;
   }

   // send method for KMH SET
   // 자녀에게 푸시 설정
   public function app_push($mb_id, $title, $message='', $url='', $type = null, $rel_id = null) {
      // $this->load->model('mq_model');

      $data = array();

      // $this->mq_model->insert(
      //    array(
      //       'mq_mb_id' => $mb_id,
      //       'mq_title' => $title,
      //       'mq_message' => $message,
      //       'mq_url' => $url,
      //       'mq_type' => $type,
      //       'mq_rel_id' => $rel_id,
      //    )
      // );
      // return true;

      // 회원 디바이스 구하기
      $this->load->model('device_model');
      $devices = $this->device_model
         ->where('dv_mb_id', $mb_id)
         ->where('dv_push', 1)
         ->get_all();

      // 푸시보내기
      foreach( (array) $devices as $key => $row ) :
         if($row->dv_id){
            $title = str_replace(';', ' ', $title);
            $message = str_replace(';', ' ', $message);
            $url = base_url($url);

            $data['post_key']     = 'title;message;url';
            $data['post_value']   = "{$title};{$message};{$url}";
            $data['mb_app_token'] = $row->dv_id;
            $data['mb_app_os']    = $row->dv_os;
            $result = $this->push_send($data);
         } else {
            return 'fail : no mb_app_token';
         }
      endforeach;

      return $result;
   }

   // TEST
   public function push_test($mb_id){
      return $this->app_push($mb_id, '푸시타이틀', '푸시메세지 - 더보기로 이동', '/home/more');
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

      // POST_DATA
      // - mb_app_os
      // - mb_app_token
      
      // DATA
      // 

      $url = 'https://fcm.googleapis.com/fcm/send';
      $headers = array(
                     'Authorization: key='.$this->api_key,
                     'Content-Type: application/json'
                  );

      if(strtolower($post_data['mb_app_os']) == 'android'){
         // $data['sound'] = '2131558400';   // 커스텀 사운드
         $fields = array (
               'data' => $data,
               'to' => $post_data['mb_app_token'],
               'priority' => 'high'
            );
      }
      elseif(strtolower($post_data['mb_app_os']) == 'ios'){
         $data['body'] =  $data['message'];
         // $data['sound'] = 'in2u_push.wav';   // 커스텀 사운드
         $data['sound'] = 'default';            // 기본 사운드
         $data['badge'] = '0';

         $fields = array (
               'data' => $data,
               'notification' => $data, //안드로이드에 함께 보낼 경우 FirebaseMessagingService call 되지 않음.
               'to' => $post_data['mb_app_token'],
               'priority' => 'high'

            );
      }

      // if(strtolower($post_data['mb_app_os']) == 'android'){
      //    $fields = array (
      //          'data' => $data,
      //          'to' => $post_data['mb_app_token'],
      //          'priority' => 'high'
      //       );
      // }
      // elseif(strtolower($post_data['mb_app_os']) == 'ios'){
      //    $data['body'] =  $data['message'];
      //    $data['sound'] = 'in2u_push.wav';
      //    $data['badge'] = '0';

      //    $fields = array (
      //          'data' => $data,
      //          'notification' => $data, //안드로이드에 함께 보낼 경우 FirebaseMessagingService call 되지 않음.
      //          'to' => $post_data['mb_app_token'],
      //          'priority' => 'high'

      //       );
      // }


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
            $result['data'] = $fields;

         }

      }


      return $result;
   }

}
