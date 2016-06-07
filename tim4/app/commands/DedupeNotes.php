<?php

use Illuminate\Console\Command;
//use Config;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class DedupeNotes extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'dedupeNotes';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'dedupeNotes';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{ 
	

		

		$notes = DB::table('dbo.TelcoCircuitNotes')
				->select(['CircuitIndexNum',DB::raw('cast([EnteredOn] as date) as EnteredOn'),DB::raw('cast([Note] as nvarchar(max)) as Note')])
				->groupBy(['CircuitIndexNum',DB::raw('cast([EnteredOn] as date)'),DB::raw('cast([Note] as nvarchar(max))')])
				->having(DB::raw('count(*)'),'>',1)->get();
		foreach($notes as $dNotes) {
			$pon = $dNotes->CircuitIndexNum;
			$date = $dNotes->EnteredOn;
			$note = $dNotes->Note;
			//echo $note;
			$id = DB::table('dbo.TelcoCircuitNotes')->select('IndexNum')
					->where('CircuitIndexNum',$pon)
					->where(DB::raw('cast([EnteredOn] as date)'),$date)
					->where(DB::raw('cast([Note] as nvarchar(max))'),$note)
					->first();
			DB::table('dbo.TelcoCircuitNotes')->where('IndexNum',$id->IndexNum)->delete();
			echo $id->IndexNum . "\n";
		}
		//echo var_dump($notes);
		
		
		
		
	}
	
	
}

