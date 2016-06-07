<?php

namespace Api;


use DB;
use Carbon;
use Input;
use Log;
use Config;

/**
 * Description of PageDataController
 *
 * @author tcassidy
 */
class PageDataController extends BaseController {
	
	public function getCustomerData() {
		
		
		
		$query = DB::table('BillingSystem.dbo.IPv4Billing');
		if (Input::get('globalLogoID')) {
			$query = $query->where('GlobalLogoID','=',Input::get('globalLogoID'));
		}
		if (Input::get('customerName')) {
			$string = '%' . Input::get('customerName') . '%';
			$query = $query->where('Customername','like',$string);
		}
		if (Input::get('firstFrom')) {
			$query = $query->where('LatestPosEmailDt','>=',Input::get('firstFrom'));
		}
		if (Input::get('firstTo')) {
			$query = $query->where('LatestPosEmailDt','<=',Input::get('firstTo'));
		}
		if (Input::get('billFrom')) {
			$query = $query->where('EffLastBillDt','>=',Input::get('billFrom'));
		}
		if (Input::get('billTo')) {
			$query = $query->where('EffLastBillDt','<=',Input::get('billTo'));
		}
		if (Input::get('AM')) {
			$query = $query->where('AM',Input::get('AM'));
		}
		$data = $query->get();
		
		return $this->response->array( $data );
	}
	
	public function getOrderData() {
		$query = DB::table('BillingSystem.dbo.IPv4BillingToOldOrder');
		if (Input::get('globalLogoID')) {
			$query = $query->where('GlobalLogoID','=',Input::get('globalLogoID'));
		}
		if (Input::get('orderId')) {
			$string = '%' . Input::get('orderId') . '%';
			$query = $query->where('OrderId','like',$string);
		}
		if (Input::get('customerName')) {
			$query2 = DB::table('BillingSystem.dbo.IPv4Billing');
			if (Input::get('customerName')) {
				$string = '%' . Input::get('customerName') . '%';
				$query2 = $query2->where('Customername','like',$string);
			}
			$gids = $query2->select(['GlobalLogoID'])->get();
			$flat = [];
			array_walk_recursive($gids, function($a) use (&$flat) { $flat[] = $a; });
			$query = $query->whereIn('GlobalLogoID',$flat);
		}
		$data = $query->get();
		return $this->response->array( $data );
		
	}
	
	public function getBlockData() {
		$query = DB::table('BillingSystem.dbo.IPv4BillingToAddresses');
		if (Input::get('globalLogoID')) {
			$query = $query->where('GlobalLogoID','=',Input::get('globalLogoID'));
		}
		if (Input::get('ipBlock')) {
			$string = '%' . Input::get('ipBlock') . '%';
			$query = $query->where('Address','like',$string);
		}
		if (Input::get('customerName')) {
			$query2 = DB::table('BillingSystem.dbo.IPv4Billing');
			if (Input::get('customerName')) {
				$string = '%' . Input::get('customerName') . '%';
				$query2 = $query2->where('Customername','like',$string);
			}
			$gids = $query2->select(['GlobalLogoID'])->get();
			$flat = [];
			array_walk_recursive($gids, function($a) use (&$flat) { $flat[] = $a; });
			$query = $query->whereIn('GlobalLogoID',$flat);
		}
		$data = $query->get();
		return $this->response->array( $data );
		
	}
	
	
}






