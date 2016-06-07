<?php


use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputOption;

class TellCogent extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'tellCogent';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'do a mail test';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$infos = DB::table('BillingSystem.dbo.IPv4Billing')->whereNotNull('CreatedOrder')->where('ToldCogent',0)->get();
		foreach($infos as $info) {
			//$info = DB::table('BillingSystem.dbo.IPv4Billing')->where('id',$id)->first(); //$id hardcoded for now
			$ccArr = ['mmckenna@cogentco.com', 'jbubeck@cogentco.com', 'vteissier@cogentco.com', 'tcassidy@cogentco.com'];
			$to = $info['AM'] . '@cogentco.com';
			$AM = $info['AM'];
			$reporting = DB::table('Reporting.dbo.RAMinfo')->select(['Director','VP','RAMMGR'])->where('RAM',$AM)->first();
			
			array_push($ccArr, $reporting['Director'] . '@cogentco.com');
			array_push($ccArr, $reporting['VP'] . '@cogentco.com');
			array_push($ccArr, $reporting['RAMMGR'] . '@cogentco.com');
			echo var_dump($ccArr);

			$customer = $info['CustomerName'];

			\Mail::send('emails.tellCogent', 
				[	
					'AM' => $info['AM'],
					'id' => $info['id'],
					'customer' => $info['CustomerName'],
				], function($message) use ($customer, $to, $ccArr)
			{
				$message
					->to([ $to ])
					->cc($ccArr)	
					->from('tcassidy@cogentco.com')
					->subject( $customer . " - Charging for Legacy IPv4 Addresses" )
					;
			});

			$this->info('Email sent');
			DB::table('BillingSystem.dbo.IPv4Billing')->where('id',$info['id'])->update(['ToldCogent' => 1]);
		}
	}
}