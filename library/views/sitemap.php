<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\sitemap.php
//
// ======================================


global $ff_response, $ff_router ?>
<?php $ff_response->startOutputBuffer() ?>
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc><?= ff_esc($ff_router->getPath('landing', [], ['mode' => 'host'])) ?></loc>
		<changefreq>daily</changefreq>
		<priority>1.0</priority>
	</url>

	<url>
		<loc><?= ff_esc($ff_router->getPath('login', [], ['mode' => 'host'])) ?></loc>
		<changefreq>daily</changefreq>
		<priority>1.0</priority>
	</url>

	<url>
		<loc><?= ff_esc($ff_router->getPath('register', [], ['mode' => 'host'])) ?></loc>
		<changefreq>daily</changefreq>
		<priority>1.0</priority>
	</url>

	<url>
		<loc><?= ff_esc($ff_router->getPath('recovery', [], ['mode' => 'host'])) ?></loc>
		<changefreq>weekly</changefreq>
		<priority>0.6</priority>
	</url>

	<url>
		<loc><?= ff_esc($ff_router->getPath('contact', [], ['mode' => 'host'])) ?></loc>
		<changefreq>weekly</changefreq>
		<priority>0.8</priority>
	</url>

	<url>
		<loc><?= ff_esc($ff_router->getPath('faq', [], ['mode' => 'host'])) ?></loc>
		<changefreq>weekly</changefreq>
		<priority>0.8</priority>
	</url>

	<url>
		<loc><?= ff_esc($ff_router->getPath('tos', [], ['mode' => 'host'])) ?></loc>
		<changefreq>weekly</changefreq>
		<priority>0.8</priority>
	</url>

	<url>
		<loc><?= ff_esc($ff_router->getPath('mlt', [], ['mode' => 'host'])) ?></loc>
		<changefreq>weekly</changefreq>
		<priority>0.8</priority>
	</url>
</urlset>
<?php $ff_response->stopOutputBuffer() ?>
