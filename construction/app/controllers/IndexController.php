<?php

class IndexController extends BaseController
{
	/**
	 * Show the view.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('index');
	}
}