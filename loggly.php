<?php
/**
 * Log to Loggly HTTPS Event Endpoint in json format
 *
 * @package default
 * @author Dave Barnwell <dave@freshsauce.co.uk>
 */
class Loggly {
  public $token;
  private $_timeout = 10;
  const LOGURL = 'https://logs-01.loggly.com/inputs/%s/tag/%s/';
  
  function __construct($token) {
    $this->token = $token;
  }
    
  function log($message,$severity="ERROR",$timestamp=null,$tag='http') {
    $data = array(  // converted to json and sent to Loggly
      'message'   => $message,
      'serverity' => $severity,
      'tag'       => $tag,
      'timestamp' => ($timestamp != null) ? $timestamp : date('c')
    );
    $s = curl_init();
    curl_setopt($s, CURLOPT_URL,sprintf(self::LOGURL,$this->token,$tag)); 
    curl_setopt($s, CURLOPT_HTTPHEADER,array('Expect:')); 
    curl_setopt($s, CURLOPT_TIMEOUT,$this->_timeout); 
    curl_setopt($s, CURLOPT_MAXREDIRS,2); 
    curl_setopt($s, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($s, CURLOPT_FOLLOWLOCATION,true);
    curl_setopt($s, CURLOPT_POST,true);
    curl_setopt($s, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($s, CURLOPT_HTTPHEADER, array('content-type:application/x-www-form-urlencoded')); 
    $json_string = curl_exec($s); 
    $status = curl_getinfo($s,CURLINFO_HTTP_CODE); 
    curl_close($s);
    if ($status != 200) throw new Exception("Failed to log http[$status]", 1);
    $json = json_decode($json_string);
    if (isset($json->response) && $json->response == 'ok') return true;
    return false;
  }
}
