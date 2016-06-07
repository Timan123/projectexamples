<?php

use Illuminate\Console\Command;
//use Config;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class DedupePhone extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'dedupePhone';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'dedupePhone';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{ 
	

		

		$calls = DB::connection('scald')->table('CallRecordsBothCA16')
				->select(['callstart','dst','src','username'])
				->groupBy(['callstart','dst','src','username'])
				->having(DB::raw('count(*)'),'>',1)->get();
		foreach($calls as $call) {
			
			//echo $note;
			$id = DB::connection('scald')->table('CallRecordsBothCA16')
					->where('callstart',$call['callstart'])
					->where('dst',$call['dst'])
					->where('src',$call['src'])
					->where('username',$call['username'])
					->select('IndexNum')
					->first();
			DB::connection('scald')->table('CallRecordsBothCA16')->where('IndexNum',$id['IndexNum'])->delete();
			
		}
		echo 'done';
		//echo var_dump($notes);
		
		
		
		
	}
	
	
}

