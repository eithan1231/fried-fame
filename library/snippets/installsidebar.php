<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\snippets\installsidebar.php
//
// ======================================


class snippets_installsidebar implements snippets_snippets
{
	/**
	* Renders the snippet
	*
	* @param array $parameters
	*		Parameters for the snippet
	*/
	public static function render(array $parameters = [])
	{
		global $ff_config, $ff_router, $ff_context;
		$language = $ff_context->getLanguage();

		?>
		<!-- <?= __CLASS__ ?> -->

		<div class="col-sm-3 offset-sm-1">
			<div>
				<h4><?= $language->getPhrase('installsidebar-about') ?></h4>
				<p>
					<?= $language->getPhrase('installsidebar-about-paragraph', [
					'project' => $ff_config->get('project-name')
					]) ?>
				</p>
			</div>
			<div>
				<h4><?= $language->getPhrase('installsidebar-platforms') ?></h4>
				<ol class="list-unstyled">
					<li><a href="<?= $ff_router->getPath('cp_install_windows') ?>"><?= $language->getPhrase('oneword-windows') ?></a></li>
					<li><a href="<?= $ff_router->getPath('cp_install_android') ?>"><?= $language->getPhrase('oneword-android') ?></a></li>
					<li><a href="<?= $ff_router->getPath('cp_install_osx') ?>"><?= $language->getPhrase('oneword-mac-osx') ?></a></li>
					<li><a href="<?= $ff_router->getPath('cp_install_linux') ?>"><?= $language->getPhrase('oneword-linux') ?></a></li>
					<li><a href="<?= $ff_router->getPath('cp_install_ios') ?>"><?= $language->getPhrase('oneword-ios') ?></a></li>
				</ol>
			</div>
			<div>
				<h4><?= $language->getPhrase('oneword-downloads') ?></h4>
				<ol class="list-unstyled">
					<li><a href="<?= $ff_router->getPath('cp_install_windows', [], [
						'hash' => 'downloads'
					]) ?>"><?= $language->getPhrase('oneword-windows-package') ?></a></li>
				</ol>
			</div>
		</div>

		<!-- <?= __CLASS__ ?> END -->

		<?php
	}
}
