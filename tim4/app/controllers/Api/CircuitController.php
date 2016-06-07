<?php

namespace Api;

use Cogent\Utility\Database;
use TelcoOrderDetails;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

/**
 * Description of VendorController
 *
 * @author tcassidy
 */
class CircuitController extends BaseController
{
	
	
	public function circuitLookupOld() {
		$circuitPiece = Input::get('circuit');
		$beginsWith = TelcoOrderDetails::whereRaw("CircuitID like '$circuitPiece%'")->select('CircuitID as value','CircuitID as label')->get();
		$contains = TelcoOrderDetails::whereRaw("CircuitID like '%$circuitPiece%'")->select('CircuitID as value','CircuitID as label')->get();
		//do this with arrays	
		$arr = $beginsWith->toArray();
		$arr2 = $contains->toArray();
		$arrMerged = $arr + $arr2;
		return $this->response->array($arrMerged);
		//return json_encode(['data' => $arrMerged]);
	}
	
	public function circuitLookup() {
		$circuitPiece = Input::get('circuit');

		$recordsBeginWith = TelcoOrderDetails::select('CircuitID')
			->where('CircuitID', 'LIKE', $circuitPiece . '%')
			->get()
			->toBase();

		$recordsContain = TelcoOrderDetails::select('CircuitID')
			->whereNotIn('CircuitID', $recordsBeginWith->fetch('CircuitID')->all())
			->where('CircuitID', 'LIKE', '%' . $circuitPiece . '%')
			->get()
			->toBase();

		// Merge together and transform
		$allRecords = $recordsBeginWith->merge( $recordsContain )
			->transform(function($entry)
			{
				$circuitID = $entry->CircuitID;

				return [ 'value' => $circuitID, 'label' => $circuitID ];
			});

		return $this->response->array( $allRecords->all() );
	}
	
		//check to see if this account exists
	public function circuitCheck() {
		$circuit = Input::get('circuit');
		$record = TelcoOrderDetails::where("CircuitID","=",$circuit)->get();
		return $this->response
			->collection($record, new \Cogent\Transformer\BaseTransformer);
		
	}
	
	//the big LCDB driven search here
	public function bigSearch() {
		$form = Input::get('form');
		//these come across as arrays
		//return $form;
		$query = DB::table('mjain.TelcoOrderDetails as tod')
				->leftJoin('mjain.TelcoNames as tel','tel.telcoid','=','tod.TelcoId')
				->leftJoin('mjain.CircuitStatusType_tbl as b','b.CircuitStatusCode','=','tod.CircuitStatus')
				->leftJoin('mjain.CircuitClassType_tbl as c','c.CircuitClassCode','=','tod.CircuitClass')
				->leftJoin('mjain.CircuitType_tbl as d','d.CircuitTypeCode','=','tod.Circuittype')
				->leftJoin('mjain.TABLE_V_SIEBELORDERS as s','s.Order_num','=','tod.OrderId') //use the base table here for speed
				->leftJoin('dbo.OPM_BillCurrency as cur','cur.BillCurrencyID','=','tod.Currency')
				->select([DB::raw('tod.CircuitId COLLATE SQL_Latin1_General_CP1253_CI_AI as CircuitId'),
					'tod.IndexNum',
					'tod.AccountNum',
					DB::raw('tod.BillingNum COLLATE SQL_Latin1_General_CP1253_CI_AI as BillingNum'),
					DB::raw('tel.TelcoName COLLATE SQL_Latin1_General_CP1253_CI_AI as TelcoName'),
					'tod.TelcoStartBillDt',
					'tod.TelcoStopBillDt',
					'tod.OrderId',
					DB::raw("replace(tod.CogentEntity, 'Cogent ', '') as CogentEntity"),
					'tod.MonthlyCost',
					'tod.InstallationCost',
					'tod.TelcoSpeed',
					'c.CircuitClassDesc as CircuitClass',
					'd.CircuitTypeDesc as CircuitType',
					'b.CircuitStatusDesc as CircuitStatus',
					'cur.CurrencyCode',
					DB::raw('s.Account  COLLATE SQL_Latin1_General_CP1253_CI_AI as CustomerName')]);
		if ($form['telco']) {
			$query->where('tod.TelcoId',$form['telco']);
		}
		if ($form['circuitClass']) {
			$query->where('tod.CircuitClass',$form['circuitClass']);
		}
		if ($form['circuitStatus']) {
			$query->where('tod.CircuitStatus',$form['circuitStatus']);
		}
		if ($form['term']) {
			$query->where('tod.Term',$form['term']);
		}
		if ($form['circuitId']) {
			$query->where('tod.CircuitId','like',str_replace('*','%',trim($form['circuitId'])));
		}
		if ($form['circuitType']) {
			$query->where('tod.CircuitType',$form['circuitType']);
		}
		if ($form['vendorAccountNum']) {
			$query->where('tod.BillingNum','like',str_replace('*','%',trim($form['vendorAccountNum'])));
		}
		if ($form['pon']) {
			$query->where('tod.IndexNum',trim($form['pon']));
		}
		if ($form['currency']) {
			$query->where('tod.Currency',$form['currency']);
		}
		if ($form['telcoSpeed']) {
			$query->where('tod.TelcoSpeed',$form['telcoSpeed']);
		}
		if ($form['orderId']) {
			$query->where('tod.OrderId','like',str_replace('*','%',trim($form['orderId'])));
		}
		if ($form['customerName']) {
			$query->where('s.Account','like',str_replace('*','%',trim($form['customerName'])));
		}
		if ($form['accountNum']) {
			$query->where('tod.AccountNum','like',str_replace('*','%',trim($form['accountNum'])));
		}
		if ($form['startBillDtFrom']) {
			$query->where('tod.TelcoStartBillDt','>=',$form['startBillDtFrom']);
		}
		if ($form['startBillDtTo']) {
			$query->where('tod.TelcoStartBillDt','<=',$form['startBillDtTo']);
		}
		if ($form['stopBillDtFrom']) {
			$query->where('tod.TelcoStopBillDt','>=',$form['stopBillDtFrom']);
		}
		if ($form['stopBillDtTo']) {
			$query->where('tod.TelcoStopBillDt','<=',$form['stopBillDtTo']);
		}
		if ($form['voucherNumber']) {
			$query->leftJoin('mjain.Invoices as inv','inv.TelcoAccNum','=','tod.AccountNum');
			$query->where('inv.GPInvoiceNum','like',str_replace('*','%',trim($form['voucherNumber'])));
		}
		if (array_key_exists('orderInProgress',$form) || $form['servCoord']) {
			//use provisioning table for speed, but it has some real junk in it, so guard against blank order join
			//tod has a lot of blank orders
			$query->leftJoin('mjain.Order_Prov_Details as p', function($join) {
				$join->on('p.OrderId','=','tod.OrderId');
				$join->on('p.OrderId', '!=', DB::raw("''"));
			});
			if ($form['servCoord']) {
				$query->where('p.ServiceCord', $form['servCoord']);
			}
			if (array_key_exists('orderInProgress',$form)) {
				$query->whereNotIn('p.StatusCode',['Completed','Cancelled','Dead','Deleted']);
			}
		}
		//max it to 2500, good number to prevent error
		//make descending by PON the default sort, so new PONs first
		$results = $query->orderBy('tod.IndexNum','desc')->take(2500)->get();
		
		$results = \Cogent\Model\BaseModel::hydrate($results);

		return $this->response
			->collection($results, new \Cogent\Transformer\BaseTransformer);
	}
	
	public function telcoNames() {
		$results = DB::select("EXEC mjain.d_getTelcoNameList" );
		return json_encode($results);
	}
	
	public function circuitStatuses() {
		$results = DB::select("EXEC mjain.d_getCircuitStatusType" );
		return json_encode($results);
	}
	
	public function circuitTypes() {
		$results = DB::select("EXEC mjain.d_getCircuitType" );
		return json_encode($results);
	}
	
	public function circuitClasses() {
		$results = DB::select("EXEC mjain.d_getCircuitClassType" );
		return json_encode($results);	
	}
	
	public function orderCurrencies() {
		$results = DB::select("EXEC dbo.d_getOPM_BillCurrency" );
		return json_encode($results);	
	}
	
	public function telcoSpeeds() {
		$results = DB::select("EXEC mjain.d_getTelcoSpeedList" );
		return json_encode($results);	
	}
	
	public function serviceCoordinators() {
		DB::statement('SET ANSI_NULLS ON; SET ANSI_WARNINGS ON');
		$results = DB::select("select 'Userid' = lower(sAMAccountName) COLLATE SQL_Latin1_General_CP1253_CI_AI from OpenQuery (ADSI, 'SELECT sAMAccountName, displayName, givenName, sn FROM ''LDAP://DC=ms,DC=cogentco,dc=com'' where memberOf = ''CN=\#OffNetProv,OU=Groups,OU=Cogent Resources,DC=ms,DC=cogentco,DC=com'' or memberOf = ''CN=\#SD,OU=Contacts,OU=Cogent Resources,DC=ms,DC=cogentco,DC=com'' or memberOf = ''CN=\#European-Provisioning,OU=Contacts,OU=Cogent Resources,DC=ms,DC=cogentco,DC=com''') order by sAMAccountName" );
		DB::statement('SET ANSI_NULLS OFF; SET ANSI_WARNINGS OFF');
		return json_encode($results);	
		
	}

	
}