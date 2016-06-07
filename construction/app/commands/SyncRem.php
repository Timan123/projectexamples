<?php


use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputOption;

class SyncRem extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cron:sync-rem';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Sync the Remedy tickets.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		DB::statement('truncate table dbo.DispatchRemTickets');
		DB::statement('truncate table dbo.DispatchEmails');
		DB::statement('truncate table dbo.RemedyMarkets');

		$results = DB::connection('remedy')->table('dbo.CGNT_HD_Help_Desk')
			->select([DB::raw("dbo.FromUnixTime(Create_Date) as 'Create_Date'"),
					DB::raw("dbo.FromUnixTime(Modified_Date) as 'Modified_Date'"),
					'Case_ID_ as Case_ID',
					'Summary',
					'Status',
					'Dispatch_Type',
					'FE_Assigned',
					'Severity',
					'Dispatch_Area',
					'Dispatch_Request_Tally',
					'FE_Group',
					'Area_Type',
					'FE_Region',
					'PendingNext'])
				->whereNotNull('Dispatch_Type')
				->take(1000)->orderBy('Modified_Date','desc')->get();


		$caseIds = [];
		foreach($results as $row) {
			$dispatchArea = $row->Dispatch_Area;
			$caseId = $row->Case_ID;
			array_push($caseIds, $caseId);
			$row = (array) $row;
			DB::table('dbo.DispatchRemTickets')->insert($row);
			

			//do market piece
			$arr = explode('-', $dispatchArea);
			foreach ($arr as $code) {
				$lookUpAirport = DB::table('dbo.AirportCodes')->where('AirportCode',$code)->select(['CogentMarket'])->first();
				if ($lookUpAirport) {
					$market = $lookUpAirport->CogentMarket;
					DB::table('dbo.RemedyMarkets')->insert(['Case_ID' => $caseId, 'CogentMarket' => $market]);
				}
			}
			
			

		}
		//do emails piece as one query
		$emails = DB::connection('remedy')->table('dbo.AR_System_Email_Messages')
			->whereRaw("Subject like '%Dispatch Request%'")->whereIn('HD_Case_ID', $caseIds)->whereRaw('Date_Sent is not null')
			->select([DB::raw("dbo.FromUnixTime(Date_Sent) as Date_Sent"), 'Subject', 'HD_Case_ID as Case_ID'])->get();
		foreach($emails as $email) {
			$email = (array) $email;
			DB::table('dbo.DispatchEmails')->insert($email);
		}

	}
}