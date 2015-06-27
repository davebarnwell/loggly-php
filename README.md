loggly
======

[![Build Status](https://scrutinizer-ci.com/g/freshsauce/loggly-php/badges/build.png?b=master)](https://scrutinizer-ci.com/g/freshsauce/loggly-php/build-status/master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/freshsauce/loggly-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/freshsauce/loggly-php/?branch=master)

Log to www.loggly.com HTTPS Event Endpoint in json format from PHP

Usage:

    $LOG = new Loggly(LOGGLY_TOKEN);  // once per session

    try {
      // do something that throws on error
      
      
    } catch (Exception $e) {
      // Send error to loggly
      $LOG->error(
        $e->getMessage()."\n".$e->getTraceAsString()."\n"
      );
    }
    
  Need to specify a few tags
  
    $LOG = new Loggly(LOGGLY_TOKEN);  // once per session
    $LOG->info(
      'your message',
      array(
        'tags' => array('tag-one','tag-two')
      )
    );


  Need to specify the timestamp and not report as now?
  
    $LOG = new Loggly(LOGGLY_TOKEN);  // once per session
    $LOG->enable(Loggly::DEBUG);      // enable debug messages
    $LOG->debug(
      "your multi \n line message",
      array(
        'timestamp' => date('c')
      )
    );
    $LOG->disable(Loggly::DEBUG);      // disable future debug messages
  
