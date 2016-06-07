@extends('layouts.default')

@section('pageHeaderTitle')
TIM
@stop

@section('javascript')
@parent
<script>
	
window.onbeforeunload = function(){
	var fromForm = app.$vm.fromForm();
	var invoiceOpen = app.$vm.invoiceOpen();
	if (!fromForm && invoiceOpen) {
		return 'You may have unsaved invoice edits, are you sure you want to leave the page?';
	} 
};
	
app.viewModel = app.baseViewModel.fullExtend(
{
	observables:
	{
		accountNum: {{json_encode($accountNum)}},
		accountNumQuery: {{json_encode($accountNum)}},
		vendorId:   {{json_encode($vendorId)}},
		circuit:    null,
		vendorDesc: null,
		pon: null,
		fromForm: false,
		fieldsLocked: false,
		region: null, //region is US or CA and should be auto-bound by vendor then a function chooses the charges dropdown based on this
		searched: false,
		invoices: [],
		accounts:	 [],
		currentInvoice: {{json_encode($invIndexNum)}},
		linesAndPons: null,
		chargeTypes: [{value: 'MRC', display: 'MRC'}, {value: 'Installation', display: 'Installation'},{value: 'Retro', display: 'Retro'},{value: 'OM', display: 'FB/PF'},{value: 'ServiceCredit',display: 'ServiceCredit'},{value: 'Misc',display: 'Misc'},{value: 'ColoRental', display: 'ColoRental'},{value: 'Labor', display: 'Labor'},{value: 'UsageFee', display: 'UsageFee'}, {value: 'TermLiability',display: 'ETL'}, {value: 'FBCredit',display: 'FBCredit'}],
		USInvChargeTypes: [	{value: 'TotalTax', display: 'Tax'},
							{value: 'MiscFee', display: 'USF'},
							{value: 'TotalLateFee', display: 'Late Fee'}],
		CAInvChargeTypes: [	{value: 'QSTTax', display: 'QST'}, {value: 'PSTTax', display: 'PST'}, {value: 'HSTTax', display: 'HST'},
			{value: 'GSTTax', display: 'GST'}, {value: 'TotalLateFee', display: 'Late Fee'}],
		//some logic will go on this one:
		invoiceStatuses: [{value: '3', display: 'Draft'},{value: '4', display: 'Submitted for Payment'}],
		disputeStatuses: [{value: 'Open'},{value: 'Awaiting Credit'}, {value: 'Closed-Won'}, {value: 'Closed-PaidBack'}],
		invoicesTotalPages:  1,
		invoiceOpen: false, //boolean for giving them a warning
		invoicesCurrentPage: 1,
		accountsTotalPages:  1,
		accountsCurrentPage: 1,
		existingInvoice: false,
		newInvoice: false, //special boolean flag for helping initialize invoice where only header is created
		hideDisconnected: true,
		dispute: {	DisputeId: null,
					CircuitId: null, //circuitid is for display purposes
					NotesToVendor: null,
		},
		categories: [], //categories to put in the dispute categories dropdown
		
		invoicesHasPrevPage: function()
		{
			return this.invoicesTotalPages() > 1 && ( this.invoicesCurrentPage() - 1 > 0 );
		},

		invoicesHasNextPage: function()
		{
			return this.invoicesTotalPages() > 1 && ( this.invoicesCurrentPage() < this.invoicesTotalPages() );
		},
		
		accountsHasPrevPage: function()
		{
			return this.accountsTotalPages() > 1 && ( this.accountsCurrentPage() - 1 > 0 );
		},

		accountsHasNextPage: function()
		{
			return this.accountsTotalPages() > 1 && ( this.accountsCurrentPage() < this.accountsTotalPages() );
		}
	},
	//put initialize first for JS debugging readability, has big autocomplete blocks in it
	initialize: function()
	{
		//shortcut code to take out or put back in
		//this.vendorId('TIME04');
		//this.accountNum('16694201');
		//this.currentInvoice(227679);
		
		$('#vendor').val(this.vendorId()).typeahead('val', this.vendorId());
		$('#circuit').val(this.circuit()).typeahead('val', this.circuit());
		$('#accountNumQuery').val(this.accountNumQuery()).typeahead('val', this.accountNumQuery());
		
		//these 3 blocks are for coming back into the application after a form submit
		var vendorId = this.vendorId();
		var accountNum = this.accountNum();
		
		if (this.vendorId() || this.accountNum()) {
			this.fromForm(true);
			this.searchBox(); //searchBox calls getAccounts
			//we need to run getAccounts even if we are only focusing on one account because this KO holds the lastInvoice associated with that account
		}
		if (this.accountNum()) {
			var obj = new Object();
			obj.TelcoAccNum = this.accountNum();
			this.viewAccount(obj,null);
			this.searched(true);
		}
		if (this.currentInvoice()) {
			//simulate the click here
			var obj = new Object();
			obj.IndexNum = this.currentInvoice();
			this.viewInvoice(obj, null);
		}

		@include('autocompletes.mainAutos')
		
	},
	
	dependencies: [{ observable: 'categories', route: 'api.getCategories', cache: true}],
	
	invChargeTypes: function() {
			if (this.region() == 'CA') {
				return this.CAInvChargeTypes();
			} else {
				return this.USInvChargeTypes();
			}
	},

	invoicesPageChange: function(direction)
	{
		direction = parseInt( direction, 10 );

		if ( !direction || ( ( direction == -1 && !this.invoicesHasPrevPage() ) || ( direction == 1 && !this.invoicesHasNextPage() ) ) )
		{
			return false;
		}

		this.invoicesCurrentPage( direction + this.invoicesCurrentPage() );
		this.getInvoices();
	},
	
	accountsPageChange: function(direction)
	{
		direction = parseInt( direction, 10 );

		if ( !direction || ( ( direction == -1 && !this.accountsHasPrevPage() ) || ( direction == 1 && !this.accountsHasNextPage() ) ) )
		{
			return false;
		}

		this.accountsCurrentPage( direction + this.accountsCurrentPage() );
		this.getAccounts();
	},
	autoTriggerFilterFormSubmit: false,
	getInvoices: function(element, event)
	{
		var accountNum = this.accountNum();

		if ( accountNum )
		{
			this.ajaxRequest
			(
				{ route: [ 'api.invoices.account', { accountNum: accountNum } ], data: { page: this.invoicesCurrentPage() } },
				function(data, json)
				{
					this.invoices( data );
					this.invoicesTotalPages( _.result(json, 'meta.pagination.total_pages', 0) );
					this.invoicesCurrentPage( _.result(json, 'meta.pagination.current_page', 0) );
				}.bind(this)
			);
		}
	},
	//searchBox is primarily about vendor and is hit first, but other stuff than vendor can be passed to it
	searchBox: function (element, event)
	{
		this.searched(true);
		var vendorId = this.vendorId();
		var circuit = this.circuit();
		var accountNum = this.accountNumQuery();
		var pon = this.pon();

		if ( vendorId || circuit || accountNum || pon)
		{
			this.ajaxRequest
			(
				{
					route: 'api.vendor',
					data:  { vendor: vendorId, circuit: circuit, accountNum: accountNum, pon: pon }
				},
				function(json)
				{
					var accountNum = this.accountNum();
					this.vendorDesc(json.vendorDesc);
					this.region(json.region);
				}.bind(this)
			);
			
		}
		this.getAccounts();
	},
	getAccounts: function(element, event) {
		var vendorId = this.vendorId();
		var circuit = this.circuit();
		var accountNum = this.accountNumQuery();
		var pon = this.pon();
		//hit the accounts separately
			this.ajaxRequest
			(
				{
					route: 'api.accounts',
					data:  { vendor: vendorId, circuit: circuit, accountNum: accountNum, page: this.accountsCurrentPage(), pon: pon }
				},
				function(data, json)
				{
					this.accounts(data);
					this.accountsTotalPages( _.result(json, 'meta.pagination.total_pages', 0) );
					this.accountsCurrentPage( _.result(json, 'meta.pagination.current_page', 0) );
					if ( data.length == 1 )	{
						this.accountNum( data[0].TelcoAccNum );
						$('#invoicesTabEntry').click();
						this.getInvoices();
					} else if (data.length == 0) {
						//if there are no accounts, blank the invoices and accounts KO
						this.accountNum(null);
						this.accounts([]);
						this.invoices([]);
						this.currentInvoice(null);
						this.linesAndPons(null);
					}
					else {
						//don't trigger this if we're coming in from the form
						//we want the invoices tab to be selected in this case
						//but set it back for future activities
						if (!this.fromForm()) {
							$('#accountTabEntry').click();
						}
						this.fromForm(false);
					}
				}.bind(this)
			);
	},
	viewAccount: function(element, event)
	{
		var accountNum = element.TelcoAccNum;

		this.accountNum(accountNum);
		this.getInvoices();

		$('#invoicesTabEntry').click();
	},
	
	viewInvoice: function(element, event)
	{
		this.invoiceOpen(true);
		this.existingInvoice(true);
		this.hideDisconnected(true);
		var scope = this;
		//use the primary key of the invoice to get it and display it
		var indexNum = element.IndexNum;
		this.ajaxRequest
		(
			{ route: [ 'api.currentInvoice', { indexNum: indexNum } ], indexNum: indexNum },	
			function(json, indexNum)
			{
				json.telcoBillingLines = _.map(json.telcoBillingLines || [], function(tblEntry)
					{
						tblEntry.Amount    = ko.observable( _.result(tblEntry, 'Amount', 0) );
						tblEntry.Disputed  = ko.observable( _.result(tblEntry, 'Disputed', 0) );
						tblEntry.Remaining = ko.observable( _.result(tblEntry, 'Remaining', 0) );

						return tblEntry;
					});

				//make the invoice level lines observables so we can live add to them
				json.telcoBillingLines = ko.observableArray(json.telcoBillingLines);
				//reset the default for the invoice status dropdown in case it was messed with earlier
				this.invoiceStatuses([{value: '3', display: 'Draft'},{value: '4', display: 'Submitted for Payment'}]);
				if (json.InvoiceStatus > 3) {
					this.fieldsLocked(true);
				} else {
					this.fieldsLocked(false); //make sure it's set to false to prevent the var from carrying over
				}
				json.InvoiceTotal = ko.observable(json.InvoiceTotal);
				json.RemainingOpenDispute = ko.observable(json.RemainingOpenDispute);
				json.AcceptedTotal = ko.observable(json.AcceptedTotal);
				json.DisputedTotal = ko.observable(json.DisputedTotal);
				if (_.result(json, 'InvoiceStatus') == 1) {
					//the invoice is new status and still needs to be initialized
					//set the default status to submitted
					json.InvoiceStatus = 4;
					//since this is a new invoice provide both status options:
					this.invoiceStatuses( [{value: '3', display: 'Draft'},{value: '4', display: 'Submitted for Payment'}] );
					this.currentInvoice(json);
					//copy the taxes/fees if any using this function, it's used twice in this case
					this.newInvLevelBillingLines();
					this.newLinesAndPons();
				} else {
					this.currentInvoice(json);
					//if the user isn't an admin and the invoice is in submitted or paid status
					@if (!Auth::user()->can('tim_admin_update_invoices'))
					if (_.result(json, 'InvoiceStatus') == 4) {
						this.invoiceStatuses([{value: '4', display: 'Submitted for Payment'}]);
					} else {
						this.invoiceStatuses( [{value: '3', display: 'Draft'},{value: '4', display: 'Submitted for Payment'}] );
					}
					@endif
					this.getLinesAndPons();
				}
				
			}.bind(this)
		);
	},
	getLinesAndPons: function(element, event) {
		var indexNum = this.currentInvoice().IndexNum;
		this.ajaxRequest
		(
			{ route: [ 'api.linesAndPons', { indexNum: indexNum } ] },	
			function(json)
			{
				json = _.map(json, function(entry)
				{
					//only hide disconnected lines with no charges

					var telcoBillingLines = _.result(entry, 'telcoBillingLines', []),
						numLines          = _.size( telcoBillingLines );
					
					telcoBillingLines = _.map(telcoBillingLines, function(tblEntry)
					{
						tblEntry.Amount    = ko.observable( _.result(tblEntry, 'Amount', 0) );
						tblEntry.Disputed  = ko.observable( _.result(tblEntry, 'Disputed', 0) );
						tblEntry.Remaining = ko.observable( _.result(tblEntry, 'Remaining', 0) );

						return tblEntry;
					});

					entry.telcoBillingLines = ko.observableArray( telcoBillingLines );
					entry.isDisconnected    = (( _.result(entry, 'telcoOrderDetails.CircuitStatus') == 'DISCONNECTED' || _.result(entry, 'telcoOrderDetails.CircuitStatus') == 'Inactive') && numLines == 0);
					//make this a computed function that sums all the charges that aren't 1 time, it will work live on the screen
					entry.billedSum = ko.computed(function()
					{
						var toSum = _.filter(entry.telcoBillingLines(), function(line) {
							if (!_.includes(['Installation','OM','Retro'], line.Type)) {
								return true;
							} else {
								return false;
							}
						});
						var billedSum = _.sum(toSum, function(line) {
							return line.Amount();
						});
						return billedSum;
					}, this);
					
					entry.display           = ko.computed(function()
					{
						if ( !entry.isDisconnected ) {	return true; }
						return this.hideDisconnected() === false;
					}, this);
					return entry;
				}, this);
				this.linesAndPons( json );
			}.bind(this)
		);
	},
	newInvoiceHeader: function(element, event) {
		this.invoiceOpen(true);
		//if you're making a new invoice, set this to false bc it will def be new info and let the warning fire
		this.fromForm(false);
		this.existingInvoice(false);
		this.hideDisconnected(true);
		
		//on a new invoice both options should be available
		this.invoiceStatuses( [{value: '3', display: 'Draft'},{value: '4', display: 'Submitted for Payment'}] );
		var accountNum = this.accountNum();
		var accounts = this.accounts();
		var invoiceDt = null;
		var dueDt = null;
		//use the latestTotalInvoice here because we want to go strictly by latest invoice, not newest non-new invoice
		var currentAccount = _.find(accounts, {'TelcoAccNum': accountNum}, 'TelcoAccNum');
		var totalDue = _.result(currentAccount, 'latestTotalInvoice.TotalDue', 0);
		var acceptedTotal = _.result(currentAccount, 'latestTotalInvoice.AcceptedTotal', 0);
		var prevBal = totalDue - acceptedTotal;
		prevBal = _.round(prevBal, 2);
		var lastDate = _.result(currentAccount, 'latestTotalInvoice.InvoiceDt', null);
		
		
		//since this is getting the latest invoice with financials, use last, not lastTotal
		
		
		if (lastDate) {
			var invoiceDt = moment(lastDate).add(1, 'M');
			var dueDt = moment(lastDate).add(2, 'M');
			invoiceDt = invoiceDt.format('MM/DD/YYYY');
			dueDt = dueDt.format('MM/DD/YYYY');
		}
		var obj = 
			{
				"IndexNum": "",
				"InvoiceNum": accountNum,
				"TelcoAccNum": accountNum,
				"InvoiceDt": invoiceDt,
				InvoiceStatus: 4, //initialize them as submitted
				"PaymentDueDt": dueDt,
				"InvoiceTotal": ko.observable(0.00),
				"AcceptedTotal": ko.observable(0.00),
				"DisputedTotal": ko.observable(0.00),
				"PrevBalance": prevBal,
				"RemainingOpenDispute": ko.observable(0.00),
				"TotalDue": null,
				telcoBillingLines: ko.observableArray([])
			};
		
		this.currentInvoice(obj);
		this.newInvLevelBillingLines();
		//call newLinesAndPons
		this.newLinesAndPons();
		//fields should always be unlocked for a new invoice
		this.fieldsLocked(false);
		
	},
	//this function is used to copy the taxes/fees from last month's invoice and apply them as
	//billinglines on the invoice level
	newInvLevelBillingLines: function(element, event) {
		var currentAccount = _.find(this.accounts(), {'TelcoAccNum': this.accountNum()}, 'TelcoAccNum');
		var lastInvIndexNum = _.result(currentAccount, 'latestInvoice.IndexNum', null);
		//if there was no last invoice to get invoice level lines from, skip this call entirely
		if (lastInvIndexNum) {
			this.ajaxRequest
			(
				{ route: [ 'api.newInvLevelBillingLines', { invIndexNum: lastInvIndexNum } ] },	
				function(json)
				{
					var telcoBillingLines = _.map(json, function(entry)
					{
							//the Type is directly from the DB on entry here, no need to set
							entry.Amount = ko.observable( _.result(entry, 'Amount', 0) );
							entry.Disputed = ko.observable( 0.00 );
							entry.Remaining = ko.observable( 0.00 );
							return entry;
					});
					this.currentInvoice().telcoBillingLines(telcoBillingLines);
				}.bind(this)
			);
		}
	},
	//this function is called for totally brand new invoices AND invoices that have been partially created header-only status=new
	newLinesAndPons: function(element, event) {
		//get these from the server since it's based on the LCDB
		this.ajaxRequest
		(
			{ route: [ 'api.newLinesAndPons', { accountNum: this.accountNum() } ] },	
			function(json)
			{
				json = _.map(json, function(entry)
				{
					var numLines = _.size(_.result(entry, 'telcoBillingLines', []));
					entry.telcoBillingLines = _.map(entry.telcoBillingLines || [], function(tblEntry)
					{
						tblEntry.Amount    = ko.observable( _.result(tblEntry, 'Amount', 0) );
						tblEntry.Disputed  = ko.observable( _.result(tblEntry, 'Disputed', 0) );
						tblEntry.Remaining = ko.observable( _.result(tblEntry, 'Remaining', 0) );

						return tblEntry;
					});

					entry.telcoBillingLines = ko.observableArray( _.result(entry, 'telcoBillingLines', []) );
					//apply the same setup to new invoice as edit invoice to allow for hide disconnected feature
					entry.isDisconnected    = (( _.result(entry, 'telcoOrderDetails.CircuitStatus') == 'DISCONNECTED' || _.result(entry, 'telcoOrderDetails.CircuitStatus') == 'Inactive') && numLines == 0);
					//make this a computed function that sums all the charges that aren't 1 time, it will work live on the screen
					entry.billedSum = ko.computed(function()
					{
						var toSum = _.filter(entry.telcoBillingLines(), function(line) {
							if (!_.includes(['Installation','OM','Retro'], line.Type)) {
								return true;
							} else {
								return false;
							}
						});
						var billedSum = _.sum(toSum, function(line) {
							return line.Amount();
						});
						return billedSum;
					}, this);
					entry.display = ko.computed(function()
					{
						if ( !entry.isDisconnected ) {	return true; }
						return this.hideDisconnected() === false;
					}, this);
					
					return entry;
				}, this);

				this.linesAndPons( json );
				
				//call reconcile totals on this new invoice to get the header initialized to appropriate values
				//do it inside callback so it happens a-sync, ontime
				this.reconcileTotals();
			}.bind(this)
		);
		
	},
		
	
	resetInvoice: function(element, event) {
		//hit the ajax calls again to set the ViewModel to what it was when they started editing in order to do the reset
		if (this.existingInvoice()) {
			var invIndexNum = this.currentInvoice().IndexNum;
			var obj = new Object();
			obj.IndexNum = invIndexNum;
			this.viewInvoice(obj, null);
		} else {
			this.newInvoiceHeader(null,null);
			this.newInvoiceLinesAndPons(null,null);
		}
	},
	addBillingLine: function(element, event) {
		this.telcoBillingLines.push({IndexNum: 0, Source: 'Charge', Type: 'MRC', Amount: ko.observable(0), Disputed: ko.observable(0), Remaining: ko.observable(0), InvIndexNum: this.InvIndexNum, ChargeIndexNum: this.IndexNum});
	},
	addInvBillingLine: function(element, event) {
		this.telcoBillingLines.push({IndexNum: 0, Source: 'Invoice', Type: 'TotalTax', Amount: ko.observable(0), Disputed: ko.observable(0), Remaining: ko.observable(0), InvIndexNum: this.InvIndexNum, ChargeIndexNum: null});
	},
	reconcileTotals: function(element, event) {
		//sum all these using jquery lookup, lodash, take out commas, set them with the observables
		var totals = $("input[name='amount[]']" );
		var sum = _.sum(totals, function(n) {
			return n.value.replace(',','');
		});
		this.currentInvoice().InvoiceTotal(sum);
		var disputed = $("input[name='disputed[]']" );
		var dispSum = _.sum(disputed, function(n) {
			return n.value.replace(',','');
		});
		this.currentInvoice().DisputedTotal(dispSum);
		var accepted = sum - dispSum;
		this.currentInvoice().AcceptedTotal(accepted);
		var remaining = $("input[name='remaining[]']" );
		var remainingSum = _.sum(remaining, function(n) {
			return n.value.replace(',','');
		});
		this.currentInvoice().RemainingOpenDispute(remainingSum);		
	},
	submitForm: function() {
		//call the reconcile totals function as if the button was pushed then let the submit go through
		this.reconcileTotals();
		//set invoiceOpen to false to allow leaving the page with no warning
		this.invoiceOpen(false);
		return true;
	},
	fillInDispute: function(parent, lineIndex, chargeLineIndex, element, event) {
		var dispute = _.result(element, 'dispute',null);
		
		//parent is the charge row
		//element is the billingline row
		
		//make it a fresh object instead if the dispute doesn't exist
		if (!dispute) {
			dispute = new Object();
		}
		dispute.IndexNumToLink = _.result(element, 'IndexNum',0);
		dispute.ChargeLineIndex = chargeLineIndex;
		dispute.LineIndex = lineIndex;
		dispute.CircuitId = _.result(parent, 'telcoOrderDetails.CircuitID','');
		dispute.Amount = _.formatNumber(_.result(element, 'Amount',0.00), 2);
		dispute.Disputed = _.formatNumber(_.result(element, 'Disputed',0.00), 2);
		dispute.Remaining = _.formatNumber(_.result(element, 'Remaining',0.00), 2);
		if (chargeLineIndex != null) {
			dispute.inv = false;
			var pon = _.result(parent, 'telcoOrderDetails.PON',null);
			var invoiceDt = _.result(parent, 'InvoiceDt', '');
			this.ajaxRequest
			(
			{ route: [ 'api.getOldDisputes', { pon: pon } ], data: {invoiceDt: invoiceDt} },	
			function(json)
			{
				if (json.length > 0) {
					
					var o = [];
					_.forEach(json, function(n, key) {
						_.forEach(n.openLines, function(n2, key2) {
							var d = new Object();
							d.InvoiceDt = n.InvoiceDt;
							d.Type = n2.Type;
							d.Status = _.result(n2, 'dispute.Status','No Dispute');
							d.Remaining = n2.Remaining;
							o.push(d);
						});
					});
					var dispute = this.dispute();
					dispute.old = o;
					this.dispute(dispute);
				}
				
			},
			this);
		} else {
			dispute.inv = true;
			var accountNum = parent.TelcoAccNum();
			var invoiceDt = parent.InvoiceDt();
			this.ajaxRequest
			(
			{ route: [ 'api.getOldDisputesInv', { accountNum: accountNum } ], data: {invoiceDt: invoiceDt} },	
			function(json)
			{
				if (json.length > 0) {
					
					var o = [];
					_.forEach(json, function(n, key) {
						_.forEach(n.openLines, function(n2, key2) {
							var d = new Object();
							d.InvoiceDt = n.InvoiceDt;
							d.Type = n2.Type;
							d.Status = _.result(n2, 'dispute.Status','No Dispute');
							d.Remaining = n2.Remaining;
							o.push(d);
						});
					});
					var dispute = this.dispute();
					dispute.old = o;
					this.dispute(dispute);
				}
				
			},
			this);
			
		}
		this.dispute(dispute);
		
	},
	modalFormSubmit: function(chargeLineIndex, lineIndex, element, event)
	{
		var formData = $('#modalForm').serializeArray();
		//pull out the line data and apply to VM, not submit to post, it's not part of the dispute table
		var lineData = _.remove(formData, function(entry) {
			return entry.name.lastIndexOf('Line', 0) === 0
		});
		var scope = this;
		var theElement = element;
		var linesAndPons = this.linesAndPons();
		
		$.ajax(
		{
			method: 'POST',
			url:    '/main/submitDispute',
			vm: this,
			theElement: theElement,
			data:   formData,
			chargeLineIndex: chargeLineIndex,
			lineIndex: lineIndex,
			lineData: lineData
		}).done(function(response)
		{
			//if there is no charge line index, it must be an invoice level line
			//these are all observables so use those to manipulate values
			if (this.chargeLineIndex == null) {
				var theLines = this.vm.currentInvoice().telcoBillingLines();
				theLines[this.lineIndex].Disputed(this.lineData[1].value);
				theLines[this.lineIndex].Remaining(this.lineData[2].value);
				theLines[this.lineIndex].dispute = response;
				this.vm.currentInvoice().telcoBillingLines(theLines);
				//jQuery id hacking is the old setup, we can keep it for this hidden field, DisputeId
				$('#didi-' + this.lineIndex).val(response.DisputeId).change();

			} else {
				var theLines = this.vm.linesAndPons()[this.chargeLineIndex].telcoBillingLines();
				theLines[this.lineIndex].Disputed(this.lineData[1].value);
				theLines[this.lineIndex].Remaining(this.lineData[2].value);
				theLines[this.lineIndex].dispute = response;
				this.vm.linesAndPons()[this.chargeLineIndex].telcoBillingLines(theLines);
				
				//jQuery id hacking is the old setup, we can keep it for this hidden field, DisputeId
				$('#didc-' + this.chargeLineIndex + '-' + this.lineIndex).val(response.DisputeId).change();
			}
			//response has the disputeId of what was just made
			//the response is the DisputeId that was updated or created
			$('#myModal [data-dismiss="modal"]').trigger('click');
			app.notify(response.message, { type: 'success' });
		}).fail(function(error)
		{
			// Assuming the server spit out an error, (ie 404, 500 status code)
			alert(error.responseText);
		});
		//do stuff don't return tru
	},
	setRemaining: function(lineIndex, chargeLineIndex, element, event) {
		var val = _.result(element, 'Disputed');
		//if you fire the change event with jquery it will update the ViewModel, do this for all val() setting
		//invoice level setting
		if (chargeLineIndex == null) {
			//invoice level setting
			var remaining = this.currentInvoice().telcoBillingLines()[lineIndex].Remaining();
			if (!remaining || remaining == 0.00) {
				this.currentInvoice().telcoBillingLines()[lineIndex].Remaining(val);
			}
		} else { 
			//charge level line setting
			var remaining = this.linesAndPons()[chargeLineIndex].telcoBillingLines()[lineIndex].Remaining();
			if (!remaining || remaining == 0.00) {
				var remaining = this.linesAndPons()[chargeLineIndex].telcoBillingLines()[lineIndex].Remaining(val);
			}
		}
		
	},
	invoiceReport: function(options) {
		var text = 'AccountNum,CircuitId,PON,InvoiceDt,AmountTotal,DisputedTotal,AuthorizedTotal,CircuitMRC,AuthMRCDiff,DiscoDate,Notes\n';
		_.forEach(this.linesAndPons(), function(n, key) {
			var amountSum = _.sum(n.telcoBillingLines(), function(line) {
				return line.Amount();
			});
			var dispSum = _.sum(n.telcoBillingLines(), function(line) {
				return line.Disputed();
			});
			var allNotes = '';
			_.forEach(n.telcoBillingLines(), function(line) {
				allNotes += line.Notes + ' ' + _.result(line, 'dispute.DisputeNotes','');
			});
			allNotes = _.trunc(allNotes,250);
			text += '="' + this.accountNum() + '",="' + n.telcoOrderDetails.CircuitID + '",' + n.PON + ',' + moment(this.currentInvoice().InvoiceDt).format('MM/DD/YYYY') + ',' + amountSum + ',' + dispSum + ',' + (amountSum - dispSum) + ',' + n.telcoOrderDetails.MRC + ',' + (amountSum - dispSum - n.telcoOrderDetails.MRC) + ',' + n.telcoOrderDetails.StopBillDt + ',"' + allNotes + '"\n';
		}, this);
		
		this.download(text);
	},
	//adapted from download function in jquery.exportTable by Shawn Dean
	download: function(dataStr)
	{
		var extension = '.csv',
			filename  = 'InvoiceReport - ' + this.accountNum() + ' - ' + moment(this.currentInvoice().InvoiceDt).format('MM-DD-YYYY');

		if (filename.substr(-1 * extension.length).toLowerCase() !== extension.toLowerCase())
		{
			filename += extension;
		}

		// Internet Explorer
		if (window.navigator && window.navigator.msSaveOrOpenBlob)
		{
			try
			{
				var blob = new Blob( [ decodeURIComponent(encodeURI(dataStr)) ], { type: 'text/csv;charset=utf-8;' });

				return window.navigator.msSaveBlob(blob, filename);
			}
			catch(e)
			{
				throw new Error('[Unable to download CSV for Internet Explorer.');
			}
		}

		// Chrome / Firefox
		try
		{
			var anchor = document.createElement('a'),
				event  = document.createEvent('MouseEvents'),
				href, encodedDataStr;

			if ( !('download' in anchor) )
			{
				throw new Error('Your browser does not support download attributes in anchor elements.');
			}

			if ( _.isFunction(window.btoa) )
			{
				encodedDataStr = window.btoa(dataStr);
			}
			else if ( _.isFunction(Base64.encode) )
			{
				encodedDataStr = Base64.encode(dataStr);
			}

			if ( !encodedDataStr )
			{
				throw new Error('Unable encode table data for download.');
			}

			href = 'data:application/csv;charset=utf-8;base64,' + encodedDataStr;

			anchor.setAttribute('download', filename);
			anchor.setAttribute('href', href);

			event.initMouseEvent('click', true, true, window, 1, 0, 0, 0, 0, false, false, false, false, 0, null);

			return anchor.dispatchEvent(event);
		}
		catch(e)
		{
			throw new Error('Unable to download CSV for Chrome / Firefox.');
		}
	},

	
});




</script>
@stop

@section("filterRow")
@stop

@section("pageHeaderButtons")
<!--put nothing here-->
@stop

@section('content')
@parent

@include('fragments.body')
@include('fragments.invoicePane')
@include('fragments.disputePopup')
@stop