loggly
======

Log to www.loggly.com HTTPS Event Endpoint in json format from PHP

Usage:

    $LOG = new Loggly(LOGGLY_TOKEN);

    try {
      // do something that throws on error
      
      
    } catch (Exception $e) {
      // Send error to loggly
      $LOG->log(
        $e->getMessage()."\n".$e->getTraceAsString()."\n"
      );
    }
  
