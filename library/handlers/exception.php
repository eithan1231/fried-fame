<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\handlers\exception.php
//
// ======================================


class handlers_exception
{
  /**
  * Registers exception handler
  */
  public static function register()
  {
    set_exception_handler('handlers_exception::handle');
  }

  /**
  * Handles an exception.
  */
  public static function handle($ex)
  {
    global $ff_response, $ff_config, $ff_context, $ff_sql;

    // I generally dislike using try/catch, but it's needed here. We really dont
    // want recuisive loops.
    try {
      $file = FF_ERR_DIR .'/'. FF_TIME .'-exception.txt';
      if($f = fopen($file, 'w+')) {
        fwrite($f, $ex->__toString());
        fclose($f);
      }
    }
    catch(Exception $ex) {
      // Stopping loop.
    }

		try {
			if(isset($ff_config) && isset($ff_response) && isset($ff_sql) && $ff_sql != null && $ff_sql->ping()) {
        $emailBuilder = new email_builder();
        $emailBuilder->setSubject($ff_config->get('project-name') .' Unhandled Exception');
        $emailBuilder->setBody($ex->__toString());
        $addresses = array_map('ff_stripEndingBlanks', explode(',', $ff_config->get('error-pushing-addresses')));
        if(count($addresses) > 0) {
          foreach($addresses as $address) {
            $emailBuilder->setRecipient($address);
          }
          $emailBuilder->send();
        }
      }
		}
		catch(Exception $ex) {

		}

		try {
      if(gettype($ex) === 'object' && get_class($ex) == 'mysqli_sql_exception') {
  			if(isset($ff_response)) {
  				$ff_response->clearBody();
  				ff_renderView('special/db');
  				$ff_response->flush();
  			}
  		}
  		else {
  			if(isset($ff_response)) {
  	      $ff_response->setHttpStatus(500);
  	      if(ff_isDevelopment()) {
  					$ff_response->setHeader("Content-type", "text/plain");
  					$ff_response->clearBody();
  					var_dump($ex);
  				}
  				else{
  					$ff_response->clearBody();
  	      }
  	      $ff_response->flush();
  	    }
  	    else if(!headers_sent()) {
  	      header('HTTP/1.1 500 Internal Error');
  				if(ff_isDevelopment()) {
  					var_dump($ex);
  				}
  	    }
  		}

      if(isset($ff_context)) {
        if($ff_context->getLogger()) {
          $ff_context->getLogger()->commit();
        }
      }
    }
    catch (Exception $ex) {

    }
    die();
  }
}
