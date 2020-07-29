<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\containers.php
//
// ======================================


class containers
{
  /**
  * Checks if a container can be viewed.
  *
  * @param string $src
  *   The route that is calling this.
  * @param request $request
  *   The request object.
  * @param response $response
  *   The response object.
  * @return bool Whether or not user can access containers
  */
  public static function canAccess(string $src, request &$request, response &$response)
  {
    global $ff_config;
    $xsrc = $request->getHeader('x-src');
    if(!$xsrc) {
      return false;
    }

    if($xsrc !== $ff_config->get('xsrc')) {
      // invalid xsrc value.
      return false;
    }

    return true;
  }

  /**
  * This should be called on all windows views. Basically checks permissions and
  * such.
  *
  * @param string $src
  *   The route that is calling this.
  * @param request $request
  *   The request object.
  * @param response $response
  *   The response object.
  * @return bool Returning false, means $Response has been modified. Otherwise true.
  */
  public static function handleWindows(string $src, request &$request, response &$response)
  {
    global $ff_context, $ff_sql, $ff_router;

    $session = $ff_context->getSession();
    $activeLink = $session->getActiveLink();
    if($src !== 'containers_windows_login' && !$activeLink) {
      // There is no user linked with session, which means nobody has logged in.
      // So lets redirect to the login page. And login page isnt' a part of the
      // control panel, so there shouldn't be a redirect loop here.
      $response->redirect($ff_router->getPath('containers_windows_login'));
      return false;
    }

    if($src !== 'containers_windows_reauth' && $activeLink['require_reauth']) {
      $response->redirect($ff_router->getPath('containers_windows_reauth'));
      return false;
    }
    else if($src !== 'containers_windows_additionalauth' && strlen($activeLink['pending_auth']) > 0) {

      // Validating the linked additional auth is valid.
      if(!additionalauth::getMethodbyName($activeLink['pending_auth'])) {
        // TODO: Ideally we shouldnt be returning here, so fix this. More checks
        // could happen towards the end of this function.
        return true;
      }

      // Additional authenticationg required, but additiona auth page isnt loaded.
      // So lets redirect there.
      $response->redirect($ff_router->getPath('containers_windows_additionalauth'));
      return false;
    }
    else if($src === 'cp_reauth' && !$activeLink['require_reauth']) {
      $response->redirect($ff_router->getPath('containers_windows_landing'));
      return false;
    }
    else if($src === 'containers_windows_additionalauth' && strlen($activeLink['pending_auth']) === 0) {
      // User is on additional authentication page, but doesnt require it.
      $response->redirect($ff_router->getPath('containers_windows_landing'));
      return false;
    }

    return true;
  }

  /**
  * This should be called on all android views. Basically checks permissions and
  * such.
  *
  * @param string $src
  *   The route that is calling this.
  * @param request $request
  *   The request object.
  * @param response $response
  *   The response object.
  * @return bool Returning false, means $Response has been modified. Otherwise true.
  */
  public static function handleAndroid(string $src, request &$request, response &$response)
  {
    global $ff_context, $ff_sql, $ff_router;

    $session = $ff_context->getSession();
    $activeLink = $session->getActiveLink();
    if($src !== 'containers_android_login' && !$activeLink) {
      // There is no user linked with session, which means nobody has logged in.
      // So lets redirect to the login page. And login page isnt' a part of the
      // control panel, so there shouldn't be a redirect loop here.
      $response->redirect($ff_router->getPath('containers_android_login'));
      return false;
    }

    if($src !== 'containers_android_reauth' && $activeLink['require_reauth']) {
      $response->redirect($ff_router->getPath('containers_android_reauth'));
      return false;
    }
    else if($src !== 'containers_android_additionalauth' && strlen($activeLink['pending_auth']) > 0) {

      // Validating the linked additional auth is valid.
      if(!additionalauth::getMethodbyName($activeLink['pending_auth'])) {
        // TODO: Ideally we shouldnt be returning here, so fix this. More checks
        // could happen towards the end of this function.
        return true;
      }

      // Additional authenticationg required, but additiona auth page isnt loaded.
      // So lets redirect there.
      $response->redirect($ff_router->getPath('containers_android_additionalauth'));
      return false;
    }
    else if($src === 'cp_reauth' && !$activeLink['require_reauth']) {
      $response->redirect($ff_router->getPath('containers_android_landing'));
      return false;
    }
    else if($src === 'containers_android_additionalauth' && strlen($activeLink['pending_auth']) === 0) {
      // User is on additional authentication page, but doesnt require it.
      $response->redirect($ff_router->getPath('containers_android_landing'));
      return false;
    }

    return true;
  }
}
