<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\snippets\containers\windows\navbar.php
//
// ======================================


class snippets_containers_windows_navbar implements snippets_snippets
{
	/**
	* Renders the snippet
	*
	* NOTE: All phrases that strat with misc, or alert, they are considered trusted.
	*
	* @param array $parameters
	*		Parameters for the snippet
	*/
	public static function render(array $parameters = [])
	{
		global $ff_config;
		?>

		<!-- <?= __CLASS__ ?> -->

    <nav id="navbar" class="navbar navbar-light bg-light noselect" style="cursor: default; width: 100%;">
			<!--
				NOTE: logoAltText must be within nested within navbar-brand, because
				navbar-brand sets the color, as does logoAltText. Nesting logoAltText will
				prioritize it.
			-->
      <span class="navbar-brand"><span class="logoAltText" style="font-size: 23px;"><?= $ff_config->get('project-name') ?></span></span>
      <div class="ml-auto">
        <button type="button" style="width: 30px; height: 30px" class="btn btn-sm btn-secondary " onclick="windows.minimize()">-</button>
        <button type="button" style="width: 30px; height: 30px" class="btn btn-sm btn-danger ml-auto" onclick="windows.close()">&times;</button>
      </div>
    </nav>

		<!-- <?= __CLASS__ ?> END -->

		<?php
	}
}
