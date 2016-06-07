<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/
Artisan::add(new \Rerun);
Artisan::add(new \VCReassign);
Artisan::add(new \GenericTest);
Artisan::add(new \AllocWork);
Artisan::add(new \SendMail);
Artisan::add(new \SendCancel);
Artisan::add(new \MailBugTest);
Artisan::add(new \BuildSFCSV);
Artisan::add(new \DoOrderBasis);

Artisan::add(new Regus\DoOrderBasisRegus);
Artisan::add(new Regus\BuildSFCSVRegus);
Artisan::add(new LinkSFtoTableRegus);
Artisan::add(new \SetUpOrdersRegus);

Artisan::add(new \OutstandingAllocAnalysis);

Artisan::add(new \MACSweep);
Artisan::add(new \SetUpOrders);
Artisan::add(new \GetOrdersFirstLast);
Artisan::add(new \LinkSFtoTable);
Artisan::add(new \StarfishReassign);
Artisan::add(new \SetupRecips);
Artisan::add(new \ChannelCheck);
Artisan::add(new \TellCogent);
Artisan::add(new \FillInPortOrder);
Artisan::add(new \LegalWork);
Artisan::add(new \LegalWorkSum);
Artisan::add(new \DedupePhone);
Artisan::add(new \HiteDocsWeb);
Artisan::add(new \QuiltDateCheck);
Artisan::add(new \OldLegacyCheck);
Artisan::add(new \NonProjBlockReassign);
Artisan::add(new \WriteEDocs);

//Artisan::add(new \GetOrders);
//Artisan::add(new \GetDiff);
//Artisan::add(new \GetFirstSet);
//Artisan::add(new \OrderResearch);
//Artisan::add(new \GetOrdersLast);
//Artisan::add(new \GetByOrder);


