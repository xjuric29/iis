<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
		$router->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');

		# Ticket overview
        $router->addRoute('<presenter>/<action>[/<page=1 \d+>]', 'Tickets:default');
		return $router;
	}
}
