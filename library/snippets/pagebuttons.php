<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\snippets\pagebuttons.php
//
// ======================================


class snippets_pagebuttons implements snippets_snippets
{
	/**
	* Renders the snippet
	*
	* @param array $parameters
	*		Parameters for the snippet
	*/
	public static function render(array $parameters = [])
	{
    $getIfSet = function($p, $d) use (&$parameters) {
      if(isset($parameters[$p])) {
        return $parameters[$p];
      }
      return $d;
    };

    $page = $getIfSet('page', 1);
    $perpage = $getIfSet('perpage', 10);
    $maxPageCount = $getIfSet('pagecount', false);
    $renderLink = $getIfSet('renderLink', function($page, $perpage, $above_id) {
      // User has not implemented a custom render link funciton, so we need to
      // do some assuming here.
      return '?'. http_build_query(array_merge($_GET, [
        'pp' => $perpage,
        'perpage' => $perpage,
        'per_page' => $perpage,
        'p' => $page,
        'page' => $page
      ]));
    });

		if(is_float($maxPageCount)) {
			$maxPageCount = intval(ceil($maxPageCount));
		}

    if($maxPageCount === false) {
      throw new Exception('expecting parameter \'count\'');
    }

    $buttonsToRender = [];

    // Rendering current page.
    $buttonsToRender[$page] = [
			'href' => $renderLink($page, $perpage, true),
			'text' => $page,
			'enabled' => true,
			'page' => $page
		];

    // Rendering pages before this page.
    for($i = $page - 1; $i > $page - 4 && $i > 0; $i--) {
      $buttonsToRender[$i] = [
				'href' => $renderLink($i, $perpage, $i > $page),
				'text' => $i,
				'enabled' => true,
				'page' => $i
			];
    }

    // Rendering after this page.
    for($i = $page + 4; $i >= $page + 1; $i--) {
			if($i > $maxPageCount) {
				continue;
			}
      $buttonsToRender[$i] = ['href' => $renderLink($i, $perpage, $i > $page), 'text' => $i, 'enabled' => true, 'page' => $i];
    }

    if($maxPageCount > 100) {
      // Render distant pages
      $base = floor($maxPageCount / 3);
      $base2 = $base * 2;

      if($base < $page) {
        for($i = $base - 1; $i > $base - 4; $i--) {
          $buttonsToRender[$i] = ['href' => $renderLink($i, $perpage, $i > $page), 'text' => $i, 'enabled' => true, 'page' => $i];
        }
      }

      if($base2 > $page) {
        for($i = $base2 + 1; $i < $base2 + 4; $i++) {
          $buttonsToRender[$i] = ['href' => $renderLink($i, $perpage, $i > $page), 'text' => $i, 'enabled' => true, 'page' => $i];
        }
      }
    }


    ksort($buttonsToRender, SORT_NUMERIC);
		array_unshift($buttonsToRender, [
			'href' => $renderLink($page - 1, $perpage, false),
			'text' => '<<',
			'enabled' => $page - 1 > 0,
			'page' => $page - 1
		]);
		array_push($buttonsToRender, [
			'href' => $renderLink($page + 1, $perpage, true),
			'text' => '>>',
			'enabled' => $page + 1 <= $maxPageCount,
			'page' => $page + 1
		]);
    ?>

		<!-- <?= __CLASS__ ?> -->
		<!-- page=<?= $page ?>;perpage=<?= $perpage ?>;maxPageCount=<?= $maxPageCount ?> -->

    <nav>
      <ul class="pagination pagination-sm">
        <?php foreach ($buttonsToRender as $pageIndex => $pageInfo): ?>
          <li class="page-item<?= $pageInfo['enabled'] ? '' : ' disabled' ?>"><a class="page-link" href="<?= ff_esc($pageInfo['href']) ?>">
            <?php if ($pageInfo['page'] == $page): ?>
              <span style="font-weight:bold;"><?= ff_esc($pageInfo['text']) ?></span>
            <?php else: ?>
              <?= ff_esc($pageInfo['text']) ?>
            <?php endif; ?>
          </a></li>
        <?php endforeach; ?>
      </ul>
    </nav>

		<!-- <?= __CLASS__ ?> END -->
		<?php
	}
}
