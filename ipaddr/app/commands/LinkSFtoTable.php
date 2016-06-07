<?php

use Illuminate\Console\Command;
//use Config;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class LinkSFtoTable extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'linkSFtoTable';

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
	

		$ids = DB::table('BillingSystem.dbo.IPv4Billing')->select(['id'])->whereRaw("isnull(ImportStep,'') = 'CSVRowCreate'")->whereNull('CreatedOrder')->get();

		foreach($ids as $id) {
			$id = $id['id'];
			$sfInfo = DB::connection('sf')->table('Opportunity')->where('Import_ID__c',$id)->select('Order__c','Id')->get();
			if ($sfInfo) {
				$createdOrder = $sfInfo[0]['Order__c'];
				$oppId = $sfInfo[0]['Id'];
				DB::table('BillingSystem.dbo.IPv4Billing')->where('id',$id)->update(['CreatedOrder' => $createdOrder, 'OppId' => $oppId, 'ImportStep' => 'LinkedFromSF']);

				echo $sfInfo[0]['Order__c'] . "\n" . $sfInfo[0]['Id'];
			}

		}
		
		
		
	}
	
	
}
