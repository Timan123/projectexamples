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
	
	public function customers() {
		$gid = Input::get("gid");
		return View::make('customers', ['gid' => $gid]);
	}
	
	public function orders() {
		$gid = Input::get("gid");
		return View::make('orders', ['gid' => $gid]);
	}
	
	public function blocks() {
		$gid = Input::get("gid");
		return View::make('blocks', ['gid' => $gid]);
	}
	
	public function email($id) {
		$emailData = EmailData::getEmailData($id);
		$viewName = $emailData['viewName'];
		return View::make('emails.' . $viewName, $emailData);
	}
}


//[	'orderString' => $string,
//					'newOrder' => $newOrder,
//					'header' => $header,
//					'mrc' => $mrc,
//					'terms' => $terms,
//					'future' => $future,
//					'billDt' => $billDt->format('F j, Y'),
//					'notifyBy' => $notifyBy->format('F j, Y'),
//					'multOrders' => $multOrders,
//					'ipCount' => $ipCount,
//					'curr' => $curr,
//					'addresses' => $addresses, 
//					'customer' => $info['CustomerName']
//			]