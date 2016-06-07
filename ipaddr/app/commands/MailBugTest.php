<?php


use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputOption;

class MailBugTest extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'mailBugTest';

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

	
		$toArr = [
			'amunson@imagit.com',
			'AnnMarie.Campanaro@regus.com',
			'Asia-noc@regus.com',
			'Budd.decosta@regus.com',
			'Canada.TelecomInvoices@regus.com',
			'carly.rutherford@dimensiondata.com',
			'carylynn.johnson@netstarinc.com',
			'cmt.regus@calero.com',
			'david.trevino@regus.com',
			'ebill@regus.com',
			'Irene.Crescuillo@calero.com',
			'itsupport@regus.com',
			'janine.depula@regus.com',
			'jason.stegman@regus.com',
			'Joanna.DiCecca@calero.com',
			'jorge.velazquez@regus.com',
			'joyel.grayson@us.didata.com',
			'justyne.weatherbee@regus.com',
			'mark.bond@regus.com',
			'meagen.hall@netstarinc.com',
			'michael.castaneda@regus.com',
			'Michelle.Ormsbee@calero.com',
			'randy.bell@regus.com',
			'regus@netstarinc.com',
			'regusorder@imagit.com',
			'regusorders@imagit.com',
			'rizalily.buning@regus.com',
			'Spencer.Jones@regus.com',
			'terry.mooney@regus.com',
			'Todd.barthell@regus.com'
		];
		$bccArr = ['dpolant@cogentco.com','tcassidy@cogentco.com'];
		$pathToFile = 'Product Rider DIA V1-9.pdf';
		$fromAddr = 'billing@cogentco.com';
		$fromName = 'Cogent Billing';
		\Mail::send('emails.regus', 
			[], function($message) use ($toArr, $bccArr, $fromAddr, $fromName, $pathToFile)
		{
			$message
				->to($toArr)
				->bcc($bccArr)	
				->attach($pathToFile, ['mime' => 'application/pdf'])
				->from($fromAddr, $fromName)
				->subject( 'Cogent Communications – Important Contract Notice' )
				;
		});
			
		
	}
}