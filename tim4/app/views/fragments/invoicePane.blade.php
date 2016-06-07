
	<!-- ko withProperties: { invoiceData: currentInvoice() } -->
	<!-- ko if: invoiceData -->
	<div id="invoiceDiv" class="panel panel-default panel-group">
		<form id='invoiceForm' action="/{{ $goBackRoutePath }}"  method='post' data-bind="submit: $root.submitForm">
		<!--put some hidden inputs on the form that allow for passing back to where we were on post-->
		<input type="hidden" name="vendorId" data-bind="value: vendorId"></input>
		<input type="hidden" name="accountNum" data-bind="value: accountNum"></input>
		<input type="hidden" name="indexNum" data-bind="value: invoiceData.IndexNum"></input>	
		<div class="row">
			<!-- ko if: invoiceData.invoiceStatusDesc -->
			
			<span class="col-md-2 form-group" ">
				<span>Current Status:<br></span>
				<span data-bind="text: invoiceData.invoiceStatusDesc"></span>
			</span>
			<!-- /ko -->
			<!--if the invoice is paid if-out this dropdown entirely -->
			<!-- ko if: invoiceData.invoiceStatusDesc != 'Paid' -->
			<span class="col-md-2 form-group">
				<select name="InvInvoiceStatus" class="input-sm form-control"
						data-bind="options: $root.invoiceStatuses, optionsValue: 'value', optionsText: 'display', value: invoiceData.InvoiceStatus">
				</select>
				
			</span>
			<!-- /ko -->
			<!-- ko if: invoiceData.LastUpDt -->
			<span class="col-md-2 form-group">
				Last Updated 
				<span data-bind="text: moment(invoiceData.LastUpDt).format('MM/DD/YYYY HH:mm')"></span> by 
				<span data-bind='text: invoiceData.LastUpUser'>	</span>
			</span>
			<!-- /ko -->
			<!-- ko if: invoiceData.CreatedDt -->
			<span class="col-md-2 form-group">
				Created 
				<span data-bind="text: moment(invoiceData.CreatedDt).format('MM/DD/YYYY HH:mm')"></span> by 
				<span data-bind='text: invoiceData.CreatedUser'>	</span>
			</span>
			<!-- /ko -->
			<span class="col-md-2 form-group">
				<button type="button" value="Invoice Report" class="form-control btn btn-sm btn-primary" data-bind="click: $root.invoiceReport.bind($root), attr: { disabled: tableData().length <= 0 }">Invoice Report</button>
				
			</span>
			<div class="col-md-2 form-group">
				<button type="button" value="Reconcile Totals" class="form-control btn btn-sm btn-primary" data-bind="click: $root.reconcileTotals">Reconcile Totals</button>
			</div>
			
			
			
		</div>
		<div class="row">
			<div class="col-md-2 form-group">
				<label class="control-label" for="InvInvoiceNum">Invoice #</label>
				<input type="text" required data-bind="attr: { readonly: $root.fieldsLocked}, value: invoiceData.InvoiceNum" name="InvInvoiceNum" id="InvInvoiceNum" class="form-control input-sm"></input>
			</div>
			<div class="col-md-2 form-group">
				<label class="control-label" for="InvInvoiceDt">Invoice Date</label>
				<input type="text" required data-bind="attr: { readonly: $root.fieldsLocked}, value: invoiceData.InvoiceDt ? moment(invoiceData.InvoiceDt).format('MM/DD/YYYY') : ''" name="InvInvoiceDt" id="InvInvoiceDt" class="date form-control input-sm" ></input>
			</div>
			<div class="col-md-2 form-group">
				<label class="control-label" for="InvPaymentDueDt">Due Date</label>
				<input type="text" required name="InvPaymentDueDt" id="InvPaymentDueDt" class="date form-control input-sm" data-bind="attr: { readonly: $root.fieldsLocked}, value: invoiceData.PaymentDueDt ? moment(invoiceData.PaymentDueDt).format('MM/DD/YYYY') : ''"/>
			</div>
			<div class="col-md-2 form-group">
				<label class="control-label" for="InvPrevBalance">Previous Balance</label>
				<input type="text" required name="InvPrevBalance" id="InvPrevBalance" class="form-control input-sm text-right" data-bind="attr: { readonly: $root.fieldsLocked}, value: invoiceData.PrevBalance, number: invoiceData.PrevBalance, decimals: 2"/>
			</div>
			<div class="col-md-2 form-group">
				<label class="control-label" for="InvAssignedTo">Assigned To</label>
				<input type="text" name="InvAssignedTo" id="InvAssignedTo" class="form-control input-sm" data-bind="value: invoiceData.AssignedTo"/>
			</div>
			<div class="col-md-2 form-group">
				<label class="control-label" for="KwikTag">KwikTag</label>&nbsp;
				<a data-bind="attr: { href: 'kwikTag?barcode=' + invoiceData.KwikTag}, text: 'View Scan', visible: invoiceData.KwikTag" target="_kwikTag"></a>
				<input type="text" data-bind="value: invoiceData.KwikTag" name="InvKwikTag" id="KwikTag" class="form-control input-sm"></input>
			</div>
			
		</div>
		<div class="row">
			<div class="col-md-2 form-group">
				<label class="control-label" for="InvInvoiceTotal">Invoice Total</label>
				<input type="text" required readonly name="InvInvoiceTotal" id="InvInvoiceTotal" class="form-control text-right input-sm" data-bind="value: invoiceData.InvoiceTotal, number: invoiceData.InvoiceTotal, decimals: 2"></input>
			</div>
			<div class="col-md-2 form-group">
				<label class="control-label" for="InvAcceptedTotal">Accepted Total</label>
				<input type="text" required readonly name="InvAcceptedTotal" id="InvAcceptedTotal" class="form-control text-right input-sm" data-bind="value: invoiceData.AcceptedTotal, number: invoiceData.AcceptedTotal, decimals: 2"></input>
			</div>
			<div class="col-md-2 form-group">
				<label class="control-label" for="InvDisputedTotal">Disputed Total</label>
				<input type="text" required readonly name="InvDisputedTotal" id="InvDisputedTotal" class="form-control text-right input-sm" data-bind="value: invoiceData.DisputedTotal, number: invoiceData.DisputedTotal, decimals: 2"></input>
			</div>
			<div class="col-md-2 form-group">
				<label class="control-label" for="InvRemainingOpenDispute">Remaining Open Dispute</label>
				<input type="text" readonly name="InvRemainingOpenDispute" id="InvRemainingOpenDispute" class="form-control text-right input-sm" data-bind="value: invoiceData.RemainingOpenDispute, number: invoiceData.RemainingOpenDispute, decimals: 2"></input>
			</div>
			<div class="col-md-2 form-group">
				<label class="control-label" for="InvTotalDue">Total Due</label>
				<input type="text" required name="InvTotalDue" id="InvTotalDue" class="form-control text-right input-sm" data-bind="attr: { readonly: $root.fieldsLocked}, value: invoiceData.TotalDue, number: invoiceData.TotalDue, decimals: 2"></input>
			</div>
			<div class="col-md-2 form-group">
				<label class="control-label" for="hideDisconnected">Hide Disconnected/No Charge</label>
				<input type="checkbox" name="hideDisconnected" value="Hide Disconnected" class="checkbox" data-bind="checked: hideDisconnected"></input>
			</div>
			
		</div>
		
		
		<table class="table table-responsive table-condensed small" id="invoiceLinesTable">
			<tbody data-bind="foreach: linesAndPons">
				<!--if there is no circuit at all for this charge, skip it, prevent JS error-->
				<!-- ko if: $data.telcoOrderDetails -->
				
				
				
				<!--put the PON on here to properly link newly made charge lines to the Circuit-->
				<input type="hidden" name="pon[]" data-bind="value: $data.telcoOrderDetails.PON"></input>
				<input type="hidden" name="indexNumForCharge[]" data-bind="value: $data.IndexNum"></input>
				<tr class="blue" data-bind="visible: $data.display">
					<th>CircuitId</th>
					<th data-bind="css: { hide: $data.telcoOrderDetails.VendorAccountNum == ''}">VendorAccNum</th>
					<th data-bind="css: { hide: $data.telcoOrderDetails.VendorAccountNum != ''}"></th>
					<th>Telco</th>
					<th>PON</th>
					<th>Billed</th>
					<th>MRC</th>
					<th>NRC</th>
					<th>CircuitStatus</th>
					<th>TelcoSpeed</th>
					<th>CircuitType</th>
					<th>CircuitClass</th>
					<th>StartBillDt</th>
					<th>StopBillDt</th>
					<th>OrderId</th>
				</tr>
				<tr data-bind="visible: $data.display">
					<td class="breaks">
						<a data-bind="attr: { href: '{{ Config::get('constants.props.provLink') . Config::get('constants.props.modPage')}}?IndexNum=' + $data.telcoOrderDetails.IndexNum}, text: _.trunc($data.telcoOrderDetails.CircuitID,20)" target="_provtool"></a></td>
					<td data-bind="css: { hide: $data.telcoOrderDetails.VendorAccountNum  == ''}"><span data-bind="text: _.trunc($data.telcoOrderDetails.VendorAccountNum,20)"></span></td>
					<td data-bind="css: { hide: $data.telcoOrderDetails.VendorAccountNum != ''}"></td>
					<td  class="breaks"><span data-bind="text: _.trunc($data.telcoOrderDetails.Telco,14)"></span></td>
					<td><span data-bind="text: $data.telcoOrderDetails.PON"></span></td>
					<td class="text-right">
						<span data-bind="number: $data.billedSum(), decimals: 2"></span>
					</td>
					<!--billedSum is a computed function that sums all the charges that aren't 1 time, use it to do the comparison to make the td red -->
					<td class="text-right" data-bind="css: { red: $data.billedSum() > $data.telcoOrderDetails.MRC}">
						<span data-bind="number: $data.telcoOrderDetails.MRC, decimals: 2"></span>
					</td>
					<!--do the same with NRC/Installation-->
					<td class="text-right" data-bind="css: { red: _.result(_.find($data.telcoBillingLines(), {'Type':'Installation'}),'Amount',0) > $data.telcoOrderDetails.NRC}">
						<span data-bind="number: $data.telcoOrderDetails.NRC, decimals: 2"></span>
					</td>  
					<td data-bind="css: { red: $data.telcoOrderDetails.CircuitStatus != 'ACTIVE' }"><span data-bind="text: $data.telcoOrderDetails.CircuitStatus"></span></td>
					<td><span data-bind="text: $data.telcoOrderDetails.TelcoSpeed"></span></td>
					<td><span data-bind="text: $data.telcoOrderDetails.CircuitClass"></span></td>
					<td><span data-bind="text: $data.telcoOrderDetails.CircuitType"></span></td>
					<td><span data-bind="text: $data.telcoOrderDetails.StartBillDt"></span></td>
					<td><span data-bind="text: $data.telcoOrderDetails.StopBillDt"></span></td>
					<td><a data-bind="attr: { href: '{{ Config::get('constants.props.provLink') . Config::get('constants.props.orderPage')}}?OrderId=' + $data.telcoOrderDetails.OrderId}, text: $data.telcoOrderDetails.OrderId" target="_provtool"></a></td>
				</tr>
				<tr class="active table-tiny" data-bind="visible: $data.display">
					<td colspan="100%" >
						<div class="row narrow">
							<span class="col-md-1" data-bind="ifnot: $root.fieldsLocked">
								<button type="button" class="glyphicon glyphicon-plus" data-bind='click: $root.addBillingLine'></button>
							</span>
							<span class="col-md-2">
					<label class="control-label" for="billingLine">Cost:</label>
					
				</span>
				<span class="col-md-2">
					<label class="control-label" for="amount">Amount:</label>
					
				</span>
				<span class="col-md-2">
					<label class="control-label" for="disputed">Disputed:</label>
				</span>
				<span class="col-md-2">
					<label class="control-label" for="remaining">Remaining:</label>
				</span>
				<span class="col-md-2">
					<label class="control-label" for="notes">Notes:</label>
				</span>			
			</div>
						
					</td>
				</tr>				
				<!-- ko foreach: $data.telcoBillingLines -->
				
				<tr data-bind="visible: $parent.display">
					<td colspan="100%" >
						<div class="row ">
							<input type="hidden" name="billingLineIndexNum[]" data-bind="value: $data.IndexNum"></input>
							<!--use the index of the parent loop to link new billing lines with the charge-->
							<input type="hidden" name="chargeLineIndex[]" data-bind="value: $parentContext.$index()"></input>
							<!--make a hidden field for back-end linking of the dispute for line items that haven't been made yet, give it an id that jquery can hook into-->
							<input type="hidden" name="disputeId[]" value="" data-bind="attr: {id: 'didc-' + $parentContext.$index() + '-' + $index()}"></input>
							<div class="col-md-1 form-group-sm">
								
							</div>
							<div class="col-md-2 form-group-sm">
								<!-- ko ifnot: $root.fieldsLocked -->
								<select name="billingLine[]" class="input-smaller input-sm form-control" 
									data-bind="options: $root.chargeTypes, optionsValue: 'value', optionsText: 'display', value: $data.Type"></select>
								<!-- /ko -->
								<!-- ko if: $root.fieldsLocked -->
								<input type="text" readonly name="billingLine[]" class="input-sm form-control input-smaller" data-bind="value: $data.Type">
								<!-- /ko -->
								
							</div>
							<div class="col-md-2 form-group-sm">
								<input type="text" name="amount[]" class="input-sm form-control text-right input-smaller" data-bind="attr: {readonly: $root.fieldsLocked}, value: $data.Amount, number: $data.Amount, decimals: 2"></input>
							</div>
							<div class="col-md-2 form-group-sm">
								<input type="text" name="disputed[]" class="input-sm form-control text-right input-smaller" data-bind="attr: {readonly: $root.fieldsLocked}, value: $data.Disputed, number: $data.Disputed, decimals: 2, event: { blur: $root.setRemaining.bind($root, $index(), $parentContext.$index()) }"></input>
							</div>
							<div class="col-md-2 form-group-sm">
								<input type="text" name="remaining[]" class="input-sm form-control text-right input-smaller" data-bind="value: $data.Remaining, number: $data.Remaining, decimals: 2"></input>
							</div>
							<div class="col-md-2 form-group-sm">
								<input type="text" name="notes[]" class="input-sm form-control input-smaller" data-bind="value: $data.Notes"></input>
							</div>
							<div class="col-md-1 form-group-sm">
								<!--derive the color from a dispute existing and remaining = 0-->
								<button type="button" class="btn btn-sm boxing-glove input-smaller" data-toggle="modal" data-target="#myModal" data-bind="click: $root.fillInDispute.bind($root, $parent, $index(), $parentContext.$index()), css: { red: ($data.dispute && $data.Remaining() > 0), green: ($data.dispute && $data.Remaining() == 0)}"></button>
							</div>
						</div>
					</td>
				</tr>
				
				<!-- /ko --><!--end billing lines loop-->
				
				
				
			<!-- /ko --><!--end ko if to make sure there's a circuit-->	
			</tbody>
			
		</table>
		<hr>
		<div class="row narrow">
				<span class="col-md-1" data-bind="ifnot: $root.fieldsLocked">
					<button type="button" class="glyphicon glyphicon-plus" data-bind='click: $root.addInvBillingLine.bind(invoiceData)'></button>
				</span>
				<span class="col-md-2">
					<label class="control-label small-label" for="billingLine">Cost:</label>
					
				</span>
				<span class="col-md-2">
					<label class="control-label small-label" for="amount">Amount:</label>
					
				</span>
				<span class="col-md-2">
					<label class="control-label small-label" for="disputed">Disputed:</label>
				</span>
				<span class="col-md-2">
					<label class="control-label small-label" for="disputed">Remaining:</label>
				</span>
				<span class="col-md-2">
					<label class="control-label small-label" for="notes">Notes:</label>
				</span>

				

			</div>
			<!-- ko foreach: invoiceData.telcoBillingLines -->
			<div class="row narrow-form-group">
				<input type="hidden" name="billingLineIndexNum[]" data-bind="value: $data.IndexNum"></input>
				<!--chargeIndexNum is blank for invoice level lines-->
				<input type="hidden" name="chargeLineIndex[]" value="-1"></input>
				<!--make a hidden field for back-end linking of the dispute for line items that haven't been made yet, give it an id that jquery can hook into-->
				<input type="hidden" name="disputeId[]" value="" data-bind="attr: {id: 'didi-' + $index()}"></input>
				<div class="col-md-1 form-group-sm">

				</div>
				<div class="col-md-2 form-group-sm">
					<!-- ko ifnot: $root.fieldsLocked -->
					<select name="billingLine[]" class="input-smaller input-sm form-control" 
						data-bind="options: $root.invChargeTypes(), optionsValue: 'value', optionsText: 'display', value: $data.Type">
					</select>
					<!-- /ko -->
					<!-- ko if: $root.fieldsLocked -->
						<input type="text" readonly name="billingLine[]" class="input-sm form-control input-smaller" data-bind="value: $data.Type">
					<!-- /ko -->
				</div>
				<div class="col-md-2 form-group-sm">

					<input type="text" name="amount[]" class="input-sm text-right form-control input-smaller" data-bind="attr: {readonly: $root.fieldsLocked}, value: $data.Amount, number: $data.Amount, decimals: 2"></input>
				</div>
				<div class="col-md-2 form-group-sm">

					<input type="text" name="disputed[]" class="input-sm text-right form-control input-smaller" data-bind="attr: {readonly: $root.fieldsLocked}, value: $data.Disputed, number: $data.Disputed, decimals: 2, event: { blur: $root.setRemaining.bind($root, $index(), null) }"></input>
				</div>
				<div class="col-md-2 form-group-sm">

					<input type="text" name="remaining[]" class="input-sm form-control text-right input-smaller" data-bind="value: $data.Remaining, number: $data.Remaining, decimals: 2"></input>
				</div>
				<div class="col-md-2 form-group-sm">
					<input type="text" name="notes[]" class="input-sm form-control input-smaller" data-bind="value: $data.Notes"></input>
				</div>
				<div class="col-md-1 form-group-sm">
					<!--derive the color from a dispute existing and remaining = 0-->
					<button type="button" class="btn btn-sm boxing-glove input-smaller" data-toggle="modal" data-target="#myModal" data-bind="click: $root.fillInDispute.bind($root, $parent, $index(), null), css: { red: ($data.dispute && $data.Remaining() > 0), green: ($data.dispute && $data.Remaining() == 0)}"  ></button>
					
				</div>
			</div>
			<!-- /ko -->
		
			<br>
			
			<button  type="submit" class="btn btn-primary" value="Save" name="Save" >
				<i class="fa fa-spinner fa-pulse" data-bind="visible: isLoading"></i>
				Save
			</button>
			<button  type="button" class="btn btn-primary pull-right" value="Reset" name="Reset" data-bind='click: $root.resetInvoice'>Reset Invoice</button>
		</form>
		
	</div>
	<!-- /ko -->
	<!-- /ko -->
