<?php
/**
 * Log to Loggly (www.loggly.com) HTTPS Event Endpoint in json format
 *
 * @author Dave Barnwell <dave@freshsauce.co.uk>
 */
class Loggly {
  public $token;
  private $_timeout     = 10;
  private $skip_logging = array('DEBUG'); // debug messages off by default
  const LOGURL          = 'https://logs-01.loggly.com/inputs/%s/tag/%s/';
  const DEBUG           = 'DEBUG';
  const INFO            = 'INFO';
  const WARNING         = 'WARNING';
  const ERROR           = 'ERROR';
  const CRITICAL        = 'CRITICAL';
  const FATAL           = 'FATAL';

  function __construct($token) {
    $this->token = $token;
  }
  
  /**
   * servity=debug log wrapper around loglevel()
   *
   * @param string $message 
   * @param array $data associative array of data to associate with message
   * @return bool
   */
  function debug($message, $data = null) {
    return $this->loglevel(self::DEBUG, $message, $data);
  }

  /**
   * servity=info log wrapper around loglevel()
   *
   * @param string $message 
   * @param array $data associative array of data to associate with message
   * @return bool
   */
  function info($message, $data = null) {
    return $this->loglevel(self::INFO, $message, $data);
  }
  
  /**
   * servity=warning log wrapper around loglevel()
   *
   * @param string $message 
   * @param array $data associative array of data to associate with message
   * @return bool
   */
  function warn($message, $data = null) {
    return $this->loglevel(self::WARNING, $message, $data);
  }
  
  /**
   * servity=error log wrapper around loglevel()
   *
   * @param string $message 
   * @param array $data associative array of data to associate with message
   * @return bool
   */
  function error($message, $data = null) {
    return $this->loglevel(self::ERROR, $message, $data);
  }
  
  /**
   * servity=critical log wrapper around loglevel()
   *
   * @param string $message 
   * @param array $data associative array of data to associate with message
   * @return bool
   */
  function critical($message, $data = null) {
    return $this->loglevel(self::CRITICAL, $message, $data);
  }

  /**
   * servity=fatal log wrapper around loglevel()
   *
   * @param string $message 
   * @param array $data associative array of data to associate with message
   * @return bool
   */
  function fatal($message, $data = null) {
    return $this->loglevel(self::FATAL, $message, $data);
  }
  
  /**
   * log wrapper around log()
   *
   * @param string $level set as severity in data passed to loggly
   * @param string $message 
   * @param array $data associative array of data to associate with message
   * @return bool
   */
  function loglevel($level, $message, $data = null) {
    if (in_array($level, $this->skip_logging)) return true; // skip levels not to log
    if (!is_array($data)) $data = array();
    $data['message']  = $message;
    $data['severity'] = $level;
    return $this->log($data);
  }
  
  /**
   * ensure future messages with specified level are logged
   *
   * @param string $level 
   * @return void
   */
  function enable($level) {
    if (($key = array_search($level, $this->skip_logging)) !== false) {
      unset($this->skip_logging[$key]);
    }
  }
  
  /**
   * don't log future messages with specified level
   *
   * @param string $level 
   * @return void
   */
  function disable($level) {
    if (!in_array($level, $this->skip_logging)) {
      $this->skip_logging[] = $level;
    }
  }
  
  /**
   * Log to loggly over https
   *
   * @param array $data associative array converted to json and sent to loggly, at minimum key 'message' should be set, optionally timestamp (ISO 8601) can be set but will default to now, and tags member is an optional array of tags to associate with the message defaults to 'http', all other data is passed through as is to loggly.
   * @return bool true on success, fale on fail, throws error if HTTP error or no message given
   */
  function log($data) {
    if (!is_array($data)) throw new Exception("Invalid params", 1);
    if (!isset($data['message'])) throw new Exception("No message given to log", 1);
    $data['timestamp'] = isset($data['timestamp']) ? $data['timestamp'] : date('c'); // default timestamp if missing
    if (!isset($data['tags'])) {
      $tags_list = 'http';
    } else {
      if (!is_array($data['tags'])) $data['tags'] = array($data['tags']); // convert to array
      $tags_list = implode(',', $data['tags']);
    }

    $s = curl_init();
    curl_setopt($s, CURLOPT_URL, sprintf(self::LOGURL, $this->token, $tags_list)); 
    curl_setopt($s, CURLOPT_HTTPHEADER, array('Expect:')); 
    curl_setopt($s, CURLOPT_TIMEOUT, $this->_timeout); 
    curl_setopt($s, CURLOPT_MAXREDIRS, 2); 
    curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($s, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($s, CURLOPT_POST, true);
    curl_setopt($s, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($s, CURLOPT_HTTPHEADER, array('content-type:application/x-www-form-urlencoded')); 
    $json_string = curl_exec($s); 
    $status = curl_getinfo($s, CURLINFO_HTTP_CODE); 
    curl_close($s);
    if ($status != 200) {
      throw new Exception("Failed to log http[$status]", 1);
    }
    $json = json_decode($json_string);
    if (isset($json->response) && $json->response == 'ok') {
      return true;
    }
    return false;
  }
}
