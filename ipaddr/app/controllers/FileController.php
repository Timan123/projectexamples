<?php

use Illuminate\Filesystem\Filesystem;

class FileController extends BaseController
{
	/**
	 * Show the view.
	 *
	 * @return Response
	 */
	public function eDocs($id)
	{
		
		$emailData = EmailData::getEmailData($id);
		$orderId = $emailData['newOrder'];
		$path = '/mnt/pdf_orders/2016/CSO_' . $orderId; 
		if(!is_dir($path)) {
			mkdir($path);
		}
		
		$fs = new Filesystem();

		$data = array();
		
		
		$viewName = $emailData['viewName'];
		$fs->put($path . '/LegacyIPv4emailcommunication.htm', View::make('emails.' . $viewName, $emailData));
		
		
		
		return $emailData;
		return View::make('index');
	}
	
}