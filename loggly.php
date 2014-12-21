<?php
/**
 * Log to Loggly HTTPS Event Endpoint in json format
 *
 * @author Dave Barnwell <dave@freshsauce.co.uk>
 */
class Loggly {
  public $token;
  private $_timeout = 10;
  const LOGURL = 'https://logs-01.loggly.com/inputs/%s/tag/%s/';
  
  function __construct($token) {
    $this->token = $token;
  }
  
  /**
   * Log to loggly over https
   *
   * @param string $message 
   * @param string $severity optional, defaults to ERROR
   * @param string|array $tag optional, defaults to HTTP, if passed an array assumed an array of string tags
   * @param string $timestamp optional, defaults to now, must be ISO 8601 date ie. date('c')
   * @return bool  returns true on success else false, throws an error if HTTP request is non 200
   * @author Dave Barnwell <dave@freshsauce.co.uk>
   */
  function log($message,$severity="ERROR",$tags='http',$timestamp=null) {
    $data = array(  // converted to json and sent to Loggly
      'message'   => $message,
      'serverity' => $severity,
      'tag'       => $tags,
      'timestamp' => ($timestamp != null) ? $timestamp : date('c')
    );
    $tags_expanded = is_array($tags) ? implode(',',$tags) : $tags;
    $s = curl_init();
    curl_setopt($s, CURLOPT_URL,sprintf(self::LOGURL,$this->token,$tags_expanded)); 
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
