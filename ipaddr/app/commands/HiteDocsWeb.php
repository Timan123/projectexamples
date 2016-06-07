<?php

use Illuminate\Console\Command;
//use Config;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class HiteDocsWeb extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'hiteDocsWeb';

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
	
		$base = 'https://ipaddr-dev.sys.cogentco.com/eDocs/';
		$ids = DB::table('BillingSystem.dbo.IPv4Billing')->whereIn('Batch',[1,2])->select('id')->get();
		foreach($ids as $id) {
			$id = $id['id'];
			$url = $base . $id;
			file_get_contents($url);
			echo "$id\n";

		}
		
		
		
	}
	
	
}
