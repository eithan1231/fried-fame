<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\snippets\navbar.php
//
// ======================================


class snippets_navbar implements snippets_snippets
{
	/**
	* Renders the snippet
	*
	* @param array $parameters
	*		Parameters for the snippet
	*/
	public static function render(array $parameters = [])
	{
		global $ff_router, $ff_context, $ff_context, $ff_request, $ff_response, $ff_config;
		$language = $ff_context->getLanguage();
		$session = $ff_context->getSession();
		$sessionLinks = $session->getLinks();
		$sessionActiveLinkUser = $session->getActiveLinkUser();
		$languages = $ff_context->getLanguages();
		$notifications = ($sessionActiveLinkUser
			? notification::getUserNotifications($sessionActiveLinkUser)
			: null
		);


		$retIfSet = function($key, $echo) use($parameters) {
			if(isset($parameters[$key])) {
				return $echo;
			}
			return '';
		};

		$buildLangAltLink = function($lang) {
			global $ff_request;

			$parsedQuery = [];
			parse_str($ff_request->getQuery(), $parsedQuery);
			$parsedQuery['__lang'] = $lang;

			return $ff_request->getPath() .'?'. http_build_query($parsedQuery);
		};

		?>

		<!-- <?= __CLASS__ ?> -->

		<nav class="navbar navbar-expand-sm bg-light navbar-light bg-light noselect">
			<?php if(isset($parameters['sidebar'])): ?>
				<a href="javascript:" class="sidebarToggle" onclick="return ff_custom.sidebar.toggle();">
					&#x2630;
				</a>
			<?php endif; ?>

			<div class="container">
				<ul class="nav navbar-nav mr-auto">

					<?php if($sessionActiveLinkUser): ?>
						<li class="nav-item<?= $retIfSet('cp_landing', ' active') ?>">
							<a class="nav-link" href="<?= $ff_router->getPath('cp_landing') ?>">
								<?= $language->getPhrase('navbar-item-cp') ?>
							</a>
						</li>
					<?php endif; ?>

					<li class="nav-item<?= $retIfSet('home', ' active') ?>">
						<a class="nav-link" href="<?= $ff_router->getPath('landing') ?>">
							<?= $language->getPhrase('navbar-item-home') ?>
						</a>
					</li>

					<?php if(!$sessionActiveLinkUser): ?>
						<li class="nav-item<?= $retIfSet('contact', ' active') ?>">
							<a class="nav-link" href="<?= $ff_router->getPath('contact') ?>">
								<?= $language->getPhrase('oneword-contact-us') ?>
							</a>
						</li>
					<?php endif; ?>
				</ul>

				<ul class="nav navbar-nav">
					<?php if (count($languages) > 1): ?>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="navbardrop_lang" data-toggle="dropdown" data-target="navbardropmenu_lang">
								<?= ff_esc($language->languageName()) ?>
							</a>
							<div class="dropdown-menu" id="navbardropmenu_lang" aria-labelledby="navbardrop_lang">
								<?php foreach($languages as $lang): ?>
									<a class="dropdown-item" href="<?= ff_esc($buildLangAltLink($lang->languageCode())) ?>">
										<img src="<?= $ff_router->getPath('asset', [
											'asset' => 'flags_'. $lang->getCountry(),
											'extension' => 'png'
										], [
											'allowForceParam' => false
										])?>" alt="<?= ff_esc($lang->languageCode()) ?>">

										<?= ff_esc($lang->languageName()) ?>
									</a>
								<?php endforeach; ?>
							</div>
						</li>
					<?php endif; ?>

					<?php if(!$sessionActiveLinkUser): ?>
						<li class="nav-item">
							<a class="nav-link" style="padding-left: 15px; padding-right: 15px" href="<?= $ff_router->getPath('register') ?>">
								<?= $language->getPhrase('navbar-item-register') ?>
							</a>
						</li>

						<li class="nav-item">
							<a class="btn btn-outline-success" style="padding-left: 15px; padding-right: 15px" href="<?= $ff_router->getPath('login') ?>">
								<strong>
									<?= $language->getPhrase('navbar-item-login') ?>
								</strong>
							</a>
						</li>
					<?php endif; ?>

					<?php if ($sessionActiveLinkUser && $notifications): ?>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="navbar-notification" data-toggle="dropdown" data-target="navbar-notifications">
								<?= $language->getPhrase('oneword-notifications') ?>
							</a>
							<div class="dropdown-menu" id='navbar-notifications' aria-labelledby="navbar-notification">
								<?php foreach ($notifications as $notification): ?>
									<?php $routeParameters = ($notification->getRouteParameters()
										? $notification->getRouteParameters()
										: []
									) ?>

									<?php $phraseParameter = ($notification->getPhraseParameters()
										? $notification->getPhraseParameters()
										: []
									) ?>
									<?php if ($notification->getRouteName()): ?>
										<a class="dropdown-item" href="<?= $ff_router->getPath($notification->getRouteName(), $routeParameters) ?>">
											<?= $language->getPhrase($notification->getPhraseName(), $phraseParameter) ?>
										</a>
									<?php else: ?>
										<span class="dropdown-item"><?= $language->getPhrase($notification->getPhraseName(), $phraseParameter) ?></span>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
						</li>
					<?php endif; ?>

					<?php if($sessionActiveLinkUser): ?>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown" data-target="navbardropmenu">
								<?= ff_esc($sessionActiveLinkUser->getUsername()) ?>
							</a>
							<div class="dropdown-menu" id='navbardropmenu' aria-labelledby="navbardrop">
								<span class="dropdown-item disabled"><?= ff_esc($sessionActiveLinkUser->getUsername()) ?></span>
								<a class="dropdown-item" href="<?= $ff_router->getPath('login') ?>" title="<?= $language->getPhrase('navbar-item-add-user-title') ?>">
									<?= $language->getPhrase('navbar-item-add-user') ?>
								</a>
								<form action="<?= ff_esc($ff_router->getPath('post', [
									'security_token' => $ff_context->getSession()->getSecurityToken(),
									'action' => 'sessionsignout'
								])) ?>" method="post">
									<button type="submit" class="dropdown-item"><?= $language->getPhrase('navbar-item-signout') ?></button>
								</form>



								<?php if (count($sessionLinks) > 1): ?>
									<div class="dropdown-divider"></div>
									<form method="post">
										<?php foreach($sessionLinks as $sessionLink): ?>
											<?php
											if($sessionActiveLinkUser->getId() === $sessionLink['user_id']) {
												// Skipping active links
												continue;
											}

											$sessionLinkUser = user::getUserById($sessionLink['user_id']);
											?>

											<!-- Enumerate profiles, so user can change linked profile -->
											<!-- TODO: Inline password entry for reauth. -->
											<button type="submit" class="dropdown-item" formaction="<?= ff_esc($ff_router->getPath('post', [
												'security_token' => $ff_context->getSession()->getSecurityToken(),
												'action' => 'sessionswitchuser'
											], [
												'query' => [
													'user' => $sessionLinkUser->getId(),
													'username' => $sessionLinkUser->getUsername()
												]
											])) ?>">
												<?php if ($sessionLink['require_reauth']): ?>
													<img src="<?= $ff_router->getPath('asset', [
														'extension' => 'png',
														'asset' => 'padlock-15x18'
													])?>" class="noselect" style="width: 7px; margin-left: -10px">
												<?php endif; ?>

												<?= ff_esc($sessionLinkUser->getUsername()) ?>

											</button>

										<?php endforeach; ?>
									</form>
								<?php endif; ?>
							</div>
						</li>
					<?php endif; ?>
				</ul>
			</div>

		</nav>

		<!-- <?= __CLASS__ ?> END -->
		<?php
	}
}
