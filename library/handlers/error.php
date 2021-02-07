<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\handlers\error.php
//
// ======================================


class handlers_error
{
  /**
  * Registers error handler
  */
  public static function register()
  {
    set_error_handler('handlers_error::handle');
  }

  /**
  * Handles an error.
  */
  public static function handle($errorNumber, $errorString, $errorFile, $errorFileLine)
  {
		global $ff_response, $ff_config, $ff_context, $ff_sql;

		// templating error
		$errorTemplated = "{$errorString}({$errorNumber}) occured at\r\n{$errorFile}\r\non line {$errorFileLine}\r\n\r\n". ff_stackTrace();

		// Saving error to file
    $file = FF_ERR_DIR .'/'. FF_TIME .'-error.txt';
    if($f = fopen($file, 'w+')) {
      fwrite($f, $errorTemplated);
      fclose($f);
    }

		// Attempting to push error email to administration
    try {
      if(
				isset($ff_config) &&
				isset($ff_response) &&
				isset($ff_sql) &&
				$ff_sql->ping()
			) {
        $emailBuilder = new email_builder();
        $emailBuilder->setSubject($ff_config->get('project-name') .' '. $errorString);
        $emailBuilder->setBody($errorTemplated);
        $addresses = array_map('ff_stripEndingBlanks', explode(',', $ff_config->get('error-pushing-addresses')));

        if(count($addresses) > 0) {
          foreach ($addresses as $address) {
            $emailBuilder->setRecipient($address);
          }
          $emailBuilder->send();
        }
      }
    }
    catch(Exception $ex) {
      // Let's not create a loop.
    }


		if(isset($ff_response)) {
			$ff_response->setHttpStatus(500);
			if(ff_isDevelopment()) {
				$ff_response->setHeader("Content-type", "text/plain");
				$ff_response->clearBody();
				$ff_response->appendbody($errorTemplated);
			}
			else{
				$ff_response->clearBody();
      }
      $ff_response->flush();
		}
		else if(!headers_sent()) {
			header('HTTP/1.1 500 Internal Error');
			if(ff_isDevelopment()) {
				echo $errorTemplated;
			}
		}

    if(isset($ff_context)) {
      if($ff_context->getLogger()) {
        $ff_context->getLogger()->commit();
      }
    }
    die();
  }
}
