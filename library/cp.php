<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\cp.php
//
// ======================================


/**
* Just a simple class to put all control panel functions here. This will prevent
* function duplication.
*/
class cp
{
  /**
  * The procedure each route in the control panel ('cp' directory) should take.
  * this is here to prevent duplication of code, and if a bug-arizes, we can
  * simply fix it here.
  *
  * @param string $src
  *   The name of the route that is calling the function.
  * @param request &$request
  *   Request object.
  * @param response &$response
  *   Response object.
  * @return bool
  *   If this function manipulates the response object, it should return false,
  *   otherwise true.
  */
  public static function standardProcedure(string $src, request &$request, response &$response)
  {
    global $ff_context, $ff_sql, $ff_router;

    // Checks if the first two characters of the $src parameter are cp (c and p).
    // All control panel routes should have their names startign with cp.
    if($src[0] !== 'c' || $src[1] !== 'p') {
      throw new Exception('Source must be from a control panel route.');
    }

    $session = $ff_context->getSession();
    $activeLink = $session->getActiveLink();
    if(!$activeLink) {
      // There is no user linked with session, which means nobody has logged in.
      // So lets redirect to the login page. And login page isnt' a part of the
      // control panel, so there shouldn't be a redirect loop here.
      $response->redirect($ff_router->getPath('login'));
      return false;
    }

    if($src !== 'cp_reauth' && $activeLink['require_reauth']) {
      // User requires reauthenticating, and this page deesnt equal to the reauth
      // page, so we need to redirect user.
      $response->redirect($ff_router->getPath('cp_reauth'));
      return false;
    }
    else if($src !== 'cp_additionalauth' && strlen($activeLink['pending_auth']) > 0) {

      // Validating the linked additional auth is valid.
      if(!additionalauth::getMethodbyName($activeLink['pending_auth'])) {
        // TODO: Ideally we shouldnt be returning here, so fix this. More checks
        // could happen towards the end of this function.
        return true;
      }

      // Additional authenticationg required, but additiona auth page isnt loaded.
      // So lets redirect there.
      $response->redirect($ff_router->getPath('cp_additionalauth'));
      return false;
    }
    else if($src === 'cp_reauth' && !$activeLink['require_reauth']) {
      // User is on reauthentication page, but doesnt require it.
      $response->redirect($ff_router->getPath('cp_landing'));
      return false;
    }
    else if($src === 'cp_additionalauth' && strlen($activeLink['pending_auth']) === 0) {
      // User is on additional authentication page, but doesnt require it.
      $response->redirect($ff_router->getPath('cp_landing'));
      return false;
    }

    return true;
  }
}
