<?php


use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputOption;

class SendCancel extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'sendCancel';

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
	
	public function fire() {
		$now = (new Carbon())->toDateTimeString();
		
		$infos = DB::table('BillingSystem.dbo.IPv4Billing')->where('id',5077)->get();
		//$infos = DB::table('BillingSystem.dbo.IPv4Billing')->whereNotNull('CreatedOrder')->whereNull('EmailSent')->get();
		//$infos = DB::table('BillingSystem.dbo.IPv4Billing')->whereNotNull('ResendRecips')->get();
//		echo count($infos);
//		return;
		
		foreach($infos as $info) {
			$id = $info['id'];
			echo $id . "\t";
			$emailData = EmailData::getEmailData($id);
			$emailData['header'] = '';
//			$pathToFile = 'Product Rider DIA V1-9.pdf';
			$viewName = 'delete';
			$fromAddr = $emailData['fromAddr'];
			$fromName = $emailData['fromName'];
			$recipsArr = $emailData['recipsArr'];
//			$bccArr = $emailData['bccArr'];
//			$recipsArr = ['tcassidy@cogentco.com'];
			$bccArr = ['tcassidy@cogentco.com','jfallen@cogentco.com','greese@cogentco.com'];
			try {
				\Mail::send('emails.' . $viewName, $emailData,
					function($message) use ($fromAddr, $fromName, $recipsArr, $bccArr)
				{
					$message
						->to($recipsArr)
						->bcc($bccArr)
//						->attach($pathToFile, ['mime' => 'application/pdf'])
						->from($fromAddr, $fromName)
						->subject( 'Cogent Communications – Followup Email' )	;
				});
				DB::table('BillingSystem.dbo.IPv4Billing')->where('id',$id)->update(['EmailSent' => $now]);
			} catch (Exception $e) {
				echo $e->getMessage();
				DB::table('BillingSystem.dbo.IPv4Billing')->where('id',$id)->update(['EmailSent' => '1-1-1900']);
			}
			
			echo $emailData['customer'] . "\n";
			
		}
		if( count(Mail::failures()) > 0 ) {
			echo "There was one or more failures. They were:\n";

			foreach(Mail::failures as $email_address) {
				echo " - $email_address\n";
			}
		}
	}
	
}
