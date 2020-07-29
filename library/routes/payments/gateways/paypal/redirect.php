<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\payments\gateways\paypal\redirect.php
//
// ======================================


class routes_payments_gateways_paypal_redirect extends route
{
	/**
	* Renders the view linked with this
	*/
	private function renderView($parameters = null)
	{
		ff_renderView(substr(__CLASS__, 7), $parameters);
	}

	/**
	* Gets the paths we want to register with this route.
	*/
	public function getPaths()
	{
		return [
      '/payments/gateways/paypal/redirect',
		];
	}

	/**
	* The name of the route.
	*/
	public function getName()
	{
		return substr(__CLASS__, 7);
	}

	/**
	* Whether or not this is a special class.
	*/
	public function isSpecial()
	{
		return false;
	}

	/**
	* Gets the supported http methods.
	*/
	public function getMethods()
	{
		return ['GET', 'HEAD'];
	}

	/**
	* The code to execute the route.
	*
	* @param array $parameters
	*		The parameters of the url.
	* @param request $request
	*		The request object.
	* @param response $response
	*		The response object.
	*/
	public function run(array $parameters, request &$request, response &$response)
	{
    global $ff_router, $ff_context, $ff_config;

		if(!$ff_config->get('paypal-enabled')) {
			// Not enabled. Let's treat the route as non-existant.
			return false;
		}

		$user = $ff_context->getSession()->getActiveLinkUser();
		if(!$user) {
			$response->redirect($ff_router->getPath('login'));
			return true;
		}

		$userGroup = $user->getGroup();
		if(!$userGroup->can('purchase')) {
			$response->redirect($ff_router->getPath('cp_landing'));
			return true;
		}

		// Getting paypal gateway.
		$paypalGateway = payment_gateway::getGateway('paypal');

		$planId = $request->get('plan_id');
		$couponCode = $request->get('coupon_code');
		$affiliateCode = $request->get('affiliate_code');
		if(!$planId) {
			$response->redirect($ff_router->getPath('cp_landing'));
			return true;
		}

		$plan = plan::getPlanById($planId);
		if(!$plan) {
			// bad plan
			$response->redirect($ff_router->getPath('cp_landing'));
			return true;
		}

		if(!$plan->getEnabled()) {
			$response->redirect($ff_router->getPath('cp_landing', [], [
				'query' => [
					'phrase' => 'misc-disabled'
				]
			]));
			return true;
		}

		$coupon = payment_coupon::getCouponByCode($couponCode);
		if(!$coupon || !$coupon->getValid()) {
			$coupon = null;
		}

		$affiliate = null;

		// building state
		$state = payment_state::newState(
			$paypalGateway,
			$user,
			$plan,
			$coupon,
			$affiliate
		);

		if(!$state) {
			throw new Exception('Failed to generate state for payments.');
		}

		$this->renderView([
			'subscription_plan' => $plan,
			'payment_state' => $state,
			'coupon' => $coupon,
			'affiliate' => $affiliate,
		]);

		return true;
	}
}
