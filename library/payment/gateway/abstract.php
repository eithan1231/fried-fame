<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\payment\gateway\abstract.php
//
// ======================================


class payment_gateway_abstract
{
	/**
	* Payment requires processing
	*
	* @param payment_state $state
	*		Payment state
	* @param array $parameters
	*		Further parameters
	*/
	public function process(payment_state $state, array $parameters)
	{
		throw new Exception('not imeplemnted');
	}
}
