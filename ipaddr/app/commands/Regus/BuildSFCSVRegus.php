<?php
namespace Regus;

use Illuminate\Console\Command;
use DB;
use Carbon;
//use Config;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class BuildSFCSVRegus extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'buildSFCSVRegus';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'IP Allocation';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{ 
		$products = [
		'EU' => '01t1a000000Gh2HAAS',
		'MX' => '01t1a000000Gh2MAAS',
		'CA' => '01t1a000000Gh2CAAS',
		'US' => '01t1a000000Gh2RAAS',
		'AP' => '01t1a000000Gh27AAC'
		];
		
		$now = (new Carbon())->toDateTimeString();
		$track = 0;
		$billings = DB::table('BillingSystem.dbo.IPv4BillingRegus')
				->whereNull('ImportStep')
				->get();
		foreach($billings as $billing) {
			$basisOrder = $billing['OrderId'];
			$id = $billing['id'];
			$ipCount = $billing['IPCount'];
			$region = $billing['Region'];
			$productCodeId = $products[$region];
			$mrc = $billing['MRC'];
			$basis = DB::connection('sf')->table('Opportunity')->where('Order__c',$basisOrder)->select
					([
					'AccountId',
					'Billing_Address_BuildingID__c',
					'Billing_Address_City__c',
					'Billing_Address_Country__c',
					'Billing_Address_Lookup__c',
					'Billing_Address_State__c',
					'Billing_Address_Zip_Code__c',
					'Billing_Address1__c',
					'Billing_Address2__c',
					'CurrencyIsoCode',
					'ForecastCategoryName',
					'RecordTypeId',
					'Sales_Engineer__c',
					'Service_Address_BuildingID__c',
					'Service_Address_City__c',
					'Service_Address_Country__c',
					'Service_Address_Lookup__c',
					'Service_Address_State__c',
					'Service_Address_Zip_Code__c',
					'Service_Address1__c',
					'Service_Address2__c',
					'Service_Address2_BuildingID__c',
					'Solution_Development_Age__c',
					'Solution_Proposed_Short_Listed_Age__c',
					'Viable_Prospect_Identified_Age__c'
					]
					)->get();
			try {
				$basis = (array) $basis[0];
			} catch (Exception $e) {
				DB::table('BillingSystem.dbo.IPv4BillingRegus')->where('id',$id)->update(['ImportStep' => 'CSVFail']);
				continue;
			}
			$basis['CreatedById'] = '0051a000001MgTd';
			$basis['LastModifiedById'] = '0051a000001MgTd';
			$basis['OwnerId'] = '0051a000001MgTd';
			$basis['Channel__c'] = 'false';
			$basis['Rejected__c'] = 'false';
			$basis['CloseDate'] = $now;
			$basis['CreatedDate'] = $now;
			$basis['LastModifiedDate'] = $now;
			$basis['Manager_Email__c'] = 'tcassidy@cogentco.com';
			$basis['Amount'] = $mrc;
			$basis['Open__c'] = 'true';
			$basis['Description'] = "Auto IPv4 Order - $region";
			$basis['Name'] = "Auto IPv4 Order - $region";
			$basis['Forecasted_MRR__c'] = $mrc;
			$basis['Forecasted_NRR__c'] = 0;
			$basis['Product_Code__c'] = $productCodeId;
			$basis['Probability'] = 100;
			$basis['Order_CDR__c'] = 0;
			$basis['Order_Status__c'] = 'Open';
			$basis['Order_Type__c'] = 'New';
			$basis['Import_ID__c'] = 'Regus' . $billing['id'];
			$basis['Import_Source__c'] = 'CSV Upload';
			$basis['Review_Notes__c'] = "Auto IPv4 Order for Regus port $basisOrder";
			$basis['StageName'] = '100% Signed Order Received';
			$basis['X100_Approved__c'] = 'true';
			
			
			if ($track == 0) {
				$heading = '';
				foreach ($basis as $key => $value) {
					$heading .= "$key,";
				}
				$heading = rtrim($heading,',');
				echo "$heading\r\n";
			}
			$row = '';
			foreach ($basis as $key => $value) {
				
				if ($value || $value == 0) {
					$row .= '"' . str_replace('"','',$value) . '",';
				} else {
					$row .= "#N/A,";
				}
			}
			$row = rtrim($row, ',');
			echo "$row\r\n";
			$track++;
			DB::table('BillingSystem.dbo.IPv4BillingRegus')->where('id',$id)->update(['ImportStep' => 'CSVRowCreate']);
			
		}
		
		
		
	}
	
	
}
