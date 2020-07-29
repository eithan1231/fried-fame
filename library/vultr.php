<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\vultr.php
//
// ======================================


class vultr
{
  const API_ENDPOINT = 'https://api.vultr.com/';

  public static function call(string $path, array $parameters = [], string $method = 'GET')
  {
    global $ff_config;

    if(!$ff_config->get('vultr-api-key')) {
      throw new Exception('Invalid vultr API key');
    }

    // Gets request
    $request = new http_request();

    // Setting API key
    $request->setHeader('API-Key', $ff_config->get('vultr-api-key'));

    // Method
    $request->setMethod($method);

    // Setting URL and Path (also builds query)
    $request->setUrl(self::API_ENDPOINT);
    $request->setPath($path .'?'. http_build_query($parameters));

    // Getting response
    $response = new http_response($request);

    switch ($response->getStatus()) {
      case 200: {
        // Success
        if(
          !$response->getHeader('content-type') ||
          $response->getHeader('content-type') !== 'application/json'
        ) {
          return ff_return(false, [], 'vultr-unknown-response');
        }

        if($res = json_decode($response->getBody(), true)) {
          if(!is_array($res)) {
            return ff_return(false, [], 'vultr-unknown-response');
          }
          return ff_return(true, $res);
        }

        return ff_return(false, [], 'vultr-unknown-response');
      }

      case 400: {
        // Invalid API location. Check the URL that you are using.
        return ff_return(false, [], 'vultr-status-400');
      }

      case 403: {
        // Invalid or missing API key. Check that your API key is present and
        // matches your assigned key.
        return ff_return(false, [], 'vultr-status-403');
      }

      case 405: {
        // Invalid HTTP method. Check that the method (POST|GET) matches what
        // the documentation indicates.
        return ff_return(false, [], 'vultr-status-405');
      }

      case 412: {
        // Request failed. Check the response body for a more detailed
        // description.
        return ff_return(false, [], 'vultr-status-412');
      }

      case 500: {
        // Internal Server Error
        return ff_return(false, [], 'vultr-status-500');
      }

      case 503: {
        // Rate limit hit. API requests are limited to an average of 2/s. Try
        // your request again later.
        return ff_return(false, [], 'vultr-status-503');
      }

      default: {
        return ff_return(false, [], 'default');
      }
    }
  }

  /**
  * Vultr has some weird shit where negative is positive, and positive is negative
  * when it comes to balances. This is just something to invert it, make it easier
  * to handle.
  *
  * NOTE: Not every price needs fixing
  */
  private static function fixCost($cost)
  {
    return -($cost);
  }

  public static function getAccountInfo()
  {
    $res = self::call('/v1/account/info');
    if($res->success) {
      $bal = floatval($res->data['balance']);
      $bal = self::fixCost($bal);
      $pendingCharges = floatval($res->data['pending_charges']);
      $remainingBalance = $bal - $pendingCharges;
      return ff_return(true, (object)[
        'balance' => $bal,
        'pendingCharges' => $pendingCharges,
        'remainingBalance' => $remainingBalance
      ]);
    }
    else {
      return ff_return(false, [], $res->messageKey);
    }
  }
}
