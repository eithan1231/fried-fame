<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\currency.php
//
// ======================================


/**
* Handle converstions and stuff alike.
*/
class currency
{
  /**
  * Gets the prefix for a currency
  *
  * @param string $currency
  *   Currency whose symbol you wanna get
  */
  public static function getCurrencyPrefix(string $currency)
  {
		// TODO: this.
    switch(strtolower($currency)) {
      case 'usd': {
        return '$';
      }

      case 'aud': {
        return 'A $';
      }

			case 'afn': {
				return '؋';
			}

			case 'amd': {
				return 'դր';
			}

			case 'azn': {
				return '₼';
			}

			case 'cyn': {
				return '¥';
			}

			case 'cyp': {
				return '£';
			}

			case 'jpy': {
				return '¥';
			}

			case 'eur': {
				return '€';
			}

      default: return '';
    }
  }

	public static function convert(string $toCurrency, float $toAmount, string $fromCurrency, float $fromAmount)
	{
		throw new Exception('Not implemented');
	}
}
