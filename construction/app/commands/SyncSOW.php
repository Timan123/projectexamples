<?php


use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputOption;


class SyncSOW extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cron:sync-sow';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Sync the SOW\'s from the NMP System.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		//$date = Carbon::today();
		DB::statement('SET ANSI_NULLS ON; SET ANSI_WARNINGS ON');
		
		DB::statement('truncate table dbo.WorkEvent');
		DB::statement('truncate table dbo.WorkEventMarkets');
		DB::statement('truncate table dbo.WorkEvent_FEs');

		$results = DB::table('Starfish.AdminSF.nmp_event')
			->select(['workid',
					'calid',
					'step',
					'last',
					'type',
					'status',
					'req_user',
					'subject',
					'location',
					'region',
					'noc',
					'na_noc_assist',
					'impact',
					'dueby',
					'start',
					'end',
					'priority',
					'net_affected',
					'cust_affected',
					'rstart',
					'rstop',
					'date',
					'user'])
				->whereRaw("(workid like 'NA%' or workid like 'EU%')")
				->where('Step',0)
				->whereRaw("date >= dateadd(m,-6,getdate())")
				->take(10000)->get();

		
		
		foreach($results as $row) {
			$row = (array) $row;
			DB::table('dbo.WorkEvent')->insert($row);
		}
		
		//get the markets, adapted from my total command line sqlsrv script
		$toLookUp = DB::table('dbo.WorkEvent')->select(['Workid', 'net_affected'])->whereRaw("datalength(net_affected) > 5")->get();
		foreach($toLookUp as $row) {
			$workId = $row->Workid;
			$hosts = explode(' ', $row->net_affected);
			$stmt = DB::table('NetInv.dbo.Devices as d')->leftJoin('TLG.dbo.Buildings as b','d.BuildingId','=','b.BuildingId')->selectRaw("distinct rtrim(CogentMarket) as 'Market'");
			$runIt = false;
			foreach ( $hosts as $host) {
				if (strlen($host) > 5) {
					$runIt = true;
					$host = ltrim($host, '-');
					$stmt->orWhere('hostname','like',$host . '%');
					//$sqlFindMarket .= "hostname like '$host%' or ";
				}
			}
	
			if ($runIt) {
				$markets = $stmt->get();
				if($markets) {
					foreach($markets as $market) {
						$market = $market->Market;
						if ($market) {
							DB::table('dbo.WorkEventMarkets')->insert(['WorkId' => $workId, 'CogentMarket' => $market]);
						}
					}
				}
			}
		}
		
		//get the field engineers for all of these
		$workIds = DB::table('dbo.WorkEvent')->select(['WorkId'])->get();
		foreach($workIds as $workId) {
			$workId = $workId->WorkId;
			$emails = DB::table('Starfish.AdminSF.nmp_user')->select(['email'])->distinct()->where('WorkId',$workId)->where('Type','E')->where('Step',0)->get();
			foreach($emails as $email) {
				$email = $email->email;
				if (!strpos($email, '@cogentco.com') || strpos($email, 'noc') === 0 || strpos($email, 'ipops') === 0) {
					continue;
				}
				//echo strpos($email, 'noc') . "\n";
				$FE = strtolower($email);
				$FE = str_replace('@cogentco.com','',$FE);
				DB::table('dbo.WorkEvent_FEs')->insert(['FE' => $FE, 'WorkId' => $workId]);
			}
		}
		
		
		DB::statement('SET ANSI_NULLS OFF; SET ANSI_WARNINGS OFF');
	}
}