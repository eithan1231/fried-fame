<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\payments\gateways\paypal\ipn.php
//
// ======================================


class routes_payments_gateways_paypal_ipn extends route
{
	/**
	* Renders the view linked with this
	*/
	private function renderView()
	{
		ff_renderView(substr(__CLASS__, 7));
	}

	/**
	* Gets the paths we want to register with this route.
	*/
	public function getPaths()
	{
		return [
      '/payments/gateways/paypal/ipn',
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
		return ['POST'];
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

		if(!payment_gateway_paypal::verifyIPN($request)) {
			// Unable to verify the connection is from paypal, so let's pretend this
			// page does not exist. hahaha!!! evil
			return false;
		}

		// Logging.
		$ff_context->getLogger()->log('Validated PayPal IPN Response', $request->getAllFields(request::METHOD_POST));

		$custom = $request->get('custom');
		$business = $request->get('business');

		if(strlen($custom) != payment_state::TOKEN_SIZE) {
			// Bad custom data. Custom data MUST be 32 characters EXACTLY.
			$response->setHttpStatus(401);// bad request
			$ff_context->getLogger()->error('Bad IPN Custom Data');
			return true;
		}

		$paymentState = payment_state::getStateByToken($custom);
		if(!$paymentState) {
			// Invalid state id, but nothing we can do.
			$response->setHttpStatus(200);
			return true;
		}

		$paypal = payment_gateway::getGateway(
			$paymentState->getPaymentGateway()
		);
		$paypal->process(
			$paymentState,
			$request->getAllFields(request::METHOD_POST)
		);

		return true;
	}
}
