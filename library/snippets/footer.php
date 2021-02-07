<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\snippets\footer.php
//
// ======================================


class snippets_footer implements snippets_snippets
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

		<footer class="container pt-4">
			<hr>
		  <div class="container-fluid">
		    <div class="row">
		      <div class="col-md-6">
		        <h5 class="text-uppercase"><?= ff_esc($ff_config->get('project-name')) ?></h5>
		        <p>
							<?= $language->getPhrase('footer-description', [
								'name' => $ff_config->get('project-name')
							]) ?>
						</p>
		      </div>

		      <div class="col-md-3">
		        <h5 class="text-uppercase"><?= $language->getPhrase('footer-information-column') ?></h5>
		        <ul class="list-unstyled">
		          <li>
		            <a href="<?= $ff_router->getPath('tos') ?>"><?= $language->getPhrase('footer-information-tos') ?></a>
		          </li>

							<li>
								<a href="<?= $ff_router->getPath('pp') ?>"><?= $language->getPhrase('footer-information-pp') ?></a>
		          </li>

							<!--<li>
		            <a href="#!">About Us</a>
		          </li>-->

							<li>
								<a href="<?= $ff_router->getPath('credits') ?>"><?= $language->getPhrase('footer-information-credits') ?></a>
		          </li>
		        </ul>
		      </div>

		      <div class="col-md-3">
		        <h5 class="text-uppercase"><?= $language->getPhrase('oneword-support') ?></h5>
		        <ul class="list-unstyled">
		          <li>
		            <a href="<?= $ff_router->getPath('contact') ?>"><?= $language->getPhrase('oneword-contact-us') ?></a>
		          </li>

							<?php if (ff_getStatusUrl()): ?>
								<li>
			            <a href="<?= $ff_router->getPath('status') ?>"><?= $language->getPhrase('oneword-status-page') ?></a>
			          </li>
							<?php endif; ?>
		        </ul>
		      </div>

		    </div>
		  </div>
			<div class="text-center py-3">
				&copy; <?= date('o', FF_TIME) ?> Copyright <a href="<?= $ff_router->getPath('landing') ?>"><?= ff_esc($ff_config->get('project-name')) ?></a>
		  </div>
		</footer>

		<!-- <?= __CLASS__ ?> END -->
		<?php
	}
}
