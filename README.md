loggly
======

Log to www.loggly.com HTTPS Event Endpoint in json format from PHP

Usage:

    $LOG = new Loggly(LOGGLY_TOKEN);  // once per session

    try {
      // do something that throws on error
      
      
    } catch (Exception $e) {
      // Send error to loggly
      $LOG->log(
        $e->getMessage()."\n".$e->getTraceAsString()."\n"
      );
    }
    
  Need to specify a few tags
  
    $LOG = new Loggly(LOGGLY_TOKEN);  // once per session
    $LOG->log('error message',array('tag-one','tag-two'));


  Need to specify the timestamp and not report as now?
  
    $LOG = new Loggly(LOGGLY_TOKEN);  // once per session
    $LOG->log('error message','tag-one',date('c'));
  
