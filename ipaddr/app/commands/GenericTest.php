<?php

use Illuminate\Console\Command;
//use Config;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class GenericTest extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'genericTest';

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
	
			
		
		$data = DB::table('BillingSystem.dbo.IPv4Billing')->where('CustomerName','like','Agentrics%')->get();
		print_r($data);
		
		
	}
	
	
}

//$customer = 'G0011a000006tvde';
//		$owner = DB::connection('sf')->table('dbo.Account as a')
//				->leftJoin('dbo.User as u','a.OwnerId','=','u.Id')
//				->where('a.GlobalLogoID__c',$customer)
//				->select('u.username')->first();
//		$username = str_ireplace('@cogentco.com','',$owner['username']);
//		$mgr = DB::table('Reporting.dbo.RAMInfo')->where('RAM',$username)->select('RAMMGR')->first();
//		$mgr = $mgr['RAMMGR'];
//		$mgr = strtolower($mgr);
//		
//		
//		echo $username . "\n";
//		echo $mgr . "\n";
//		
//		//primary technical, billing, administrative, for all orders on customer, check active flags
//		$recips = DB::table('TLG.dbo.CCDB as c')
//			->leftJoin('TLG.dbo.CCDBCCToOrder as co','c.CCID','=','co.CCID')
//			->whereIn('co.OrderId',function($query) use ($customer) {
//				$query->from('TLG.dbo.v_sdscorecard')
//					->select('OrderId')
//					->where('GlobalLogoID',$customer)
//					->where('ProvOrderStatus','Completed')
//					->whereNull('OrderCancelDt');
//				})
//			->where('c.IsActive',1)
//			->where('co.IsActive',1)
//			->whereIn('co.ContactType',[1,9,23])->select('c.PrimaryEmail')->distinct()->get();
//		echo var_dump($recips);


		//$result = DB::connection('dev')->select('select @@SERVERNAME');
		//echo json_encode($result);
//		$smbc = new smbclient ('//iad-web2','TomcatUser','PSICanWrite!');
//		//$smbc->put('junk.txt','IndexSystem\PDF_ORDERS\2016\CSO_1-300054318');
//		if (!$smbc->get ('IndexSystem\PDF_ORDERS\2016\', 'Test.pdf'))
//		{
//			print "Failed to retrieve file:\n";
//			print join ("\n", $smbc->get_last_cmd_stderr());
//		}
		
//		$excludeSet = Config::get('constants.excludeSet');
//		$customers = DB::table('TLG.dbo.v_sdscorecard as sd')->whereIn('OrderId',function($query) {
//				$query->from('Starfish.AdminSF.ip_block')->select('OrderId')->distinct();
//
//			})
//			->whereNotIn('GlobalLogoID',$excludeSet)
//			->select('GlobalLogoID')->distinct()->get();
//		echo count($customers);
//		$i = 0;
//		foreach($customers as $customer) {
//			$i++;
//			//echo $customer['GlobalLogoID'] . "\n";
//		}
//		echo $i;