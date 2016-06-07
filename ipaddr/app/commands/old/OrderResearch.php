<?php

use Illuminate\Console\Command;
//use Config;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class OrderResearch extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'orderResearch';

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
	
		$bigArray = Config::get('constants.alllocCustMRC');
		
		foreach($bigArray as $data) {
			$customer = $data['customer'];
			$count = DB::connection('sf')->table('Account')->where('GlobalLogoID__c',$customer)->count();
			if ($count != 1) {
				echo "$customer\n";
			}
			
		}
		
		
	}
	
	
}
