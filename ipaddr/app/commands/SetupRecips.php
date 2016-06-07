<?php

use Illuminate\Console\Command;
//use Config;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class SetupRecips extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'setupRecips';

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
	
		$customers = DB::table('BillingSystem.dbo.IPv4Billing')
				->select(['id','GlobalLogoID'])
				->where('id',2443)
				//->whereIn('Batch',[8,9])
				//->where('Recips','')
				->get();
		foreach($customers as $customer) {
		
			$id = $customer['id'];
			$customer = $customer['GlobalLogoID'];
			$owner = DB::connection('sf')->table('dbo.Account as a')
					->leftJoin('dbo.User as u','a.OwnerId','=','u.Id')
					->where('a.GlobalLogoID__c',$customer)
					->select('u.username')->first();
			$username = str_ireplace('@cogentco.com','',$owner['username']);
			$mgr = DB::table('Reporting.dbo.RAMInfo')->where('RAM',$username)->select('RAMMGR')->first();
			$mgr = $mgr['RAMMGR'];
			$mgr = strtolower($mgr);

		

			//primary technical, billing, administrative, for all orders on customer, check active flags
			$recips = DB::table('TLG.dbo.CCDB as c')
				->leftJoin('TLG.dbo.CCDBCCToOrder as co','c.CCID','=','co.CCID')
				->whereIn('co.OrderId',function($query) use ($customer) {
					$query->from('TLG.dbo.v_sdscorecard')
						->select('OrderId')
						->where('GlobalLogoID',$customer)
						->where('ProvOrderStatus','Completed')
						->whereNull('OrderCancelDt');
					})
				->where('c.IsActive',1)
				->where('co.IsActive',1)
				->whereRaw("c.PrimaryEmail like '%@%'")
				->whereIn('co.ContactType',[1,9,23])
				->select('c.PrimaryEmail')->distinct()->get();
			$recipString = '';
			foreach($recips as $recip) {
				$recip = str_replace(' ','',$recip);
				$recipString .= $recip['PrimaryEmail'] . ',';
			}
			$recipString = chop($recipString,',');
//			echo "$recipString\n";
			DB::table('BillingSystem.dbo.Ipv4Billing')
				->where('id',$id)
				->update([	'Recips' => $recipString,
							'AM' => $username,
							'MGR' => $mgr
						]);
			echo "$customer\n";
		}
		
		
		
	}
	
	
}

