<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\snippets\cssincl.php
//
// ======================================


class snippets_cssincl implements snippets_snippets
{
	/**
	* Renders the snippet
	*
	* @param array $parameters
	*		Parameters for the snippet
	*/
	public static function render(array $parameters = [])
	{
		global $ff_router, $ff_context, $ff_response;

		$paramSet = function($key) use($parameters) {
			return isset($parameters[$key]);
		};

    $assets = [
			'bootstrap',
			'custom'
		];
    if($paramSet('include')) {
      $assets = array_merge($assets, $parameters['include']);
    }

		?>

		<!-- <?= __CLASS__ ?> -->

    <?php foreach ($assets as $value): ?>
			<?php
			if(substr($value, 0, 4) === 'http') {
				// assume it's a link, not asset name.
				?><link rel="stylesheet" href="<?= ff_esc($value) ?>" /><?php
				continue;
			}
			?>
  		<link rel="stylesheet" href="<?= $ff_router->getPath('asset', [
  			'asset' => $value,
  			'extension' => 'css'
  		], ['allowForceParam' => false]) ?>" />
    <?php endforeach; ?>

		<!-- <?= __CLASS__ ?> END -->

		<?php
	}
}
