<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel" data-bind="text: dispute().DisputeId ? 'Edit Dispute' : 'Create Dispute'"></h4>
			</div>

			<form action="#" method="get" id="modalForm" data-bind="submit: modalFormSubmit.bind($root, dispute().ChargeLineIndex, dispute().LineIndex)">
				<div class="modal-body">
					<!--put the billing line on here to do with linkage-->
					<input type="hidden" name="IndexNumToLink" data-bind="value: dispute().IndexNumToLink"></input>
					
					<label for="DisputeId" class="control-label">Dispute ID:</label> 
					<span class="input-sm input-smaller no-border" data-bind="text: dispute().DisputeId"></span>
					<input type="hidden" name="DisputeId" data-bind="value: dispute().DisputeId"></input>
					<br>
					<label for="accountNum" class="control-label">Account Number:</label>
					<span class="input-sm input-smaller no-border" name="accountNum" id="accountNum" data-bind="text: accountNum"></span>
					<br>
					<label for="invoiceNum" class="control-label">Invoice:</label>
					<span class="input-sm input-smaller no-border" name="invoiceNum" id="invoiceNum" data-bind="text: _.result(currentInvoice(), 'InvoiceNum', '')"></span>
					&nbsp;
					<label for="circuit" class="control-label" data-bind="if: dispute().CircuitId">Circuit:</label>
					<span class="input-sm input-smaller no-border" name="circuit" id="invoiceNum" data-bind="text: dispute().CircuitId"></span>
					<br>
					<span class="input-sm input-smaller no-border" name="circuit" id="invoiceNum" data-bind="text: dispute().PON"></span>
					<br>
					<table class="table-borderless table-condensed">
						<tr class="table-tiny">
							<td><label for="amount"  >Amount:</label></td>
							<td><label for="disputed" >Disputed:</label></td>
							<td><label for="remaining" >Remaining:</label></td>
						</tr>
						<tr>
							<td><input type="text" readonly class="input-sm input-smaller form-control" name="LineAmount" id="amount" data-bind="value: dispute().Amount, number: dispute().Amount, decimals: 2"></input></td>
							<td><input type="text" class="input-sm input-smaller form-control" name="LineDisputed" id="disputed" data-bind="attr: { readonly: $root.fieldsLocked}, value: dispute().Disputed, number: dispute().Disputed, decimals: 2"></input></td>
							<td><input type="text" class="input-sm input-smaller form-control" name="LineRemaining" id="remaining" data-bind="value: dispute().Remaining, number: dispute().Remaining, decimals: 2"></input></td>
						</tr>
						
					</table>
					<label for="category" class="control-label">Category:</label>
					<select name="CategoryId" class="input-sm input-smaller form-control form-control-lite" data-bind="options: $root.categories, optionsValue: 'CategoryId', optionsText: 'Category', value: dispute().CategoryId"></select>
					<br>
					<label for="status" class="control-label">Status:</label>
					<select name="Status" class="input-sm input-smaller form-control form-control-lite" data-bind="options: $root.disputeStatuses, optionsValue: 'value', optionsText: 'value', value: dispute().Status"></select>
					<br>
					<label for="vendorDisputeId" class="control-label">Vendor Dispute Num:</label>
					<input type="text" class="input-sm input-smaller form-control form-control-lite" name="VendorDisputeNum" id="VendorDisputeNum" data-bind="value: dispute().VendorDisputeNum"></input>
					<br>
					<label for="WonAmount" class="control-label">Won Amount:</label>
					<input type="text" class="input-sm input-smaller form-control form-control-lite" name="WonAmount" id="WonAmount" data-bind="value: _.result(dispute(), 'WonAmount', 0), number: _.result(dispute(), 'WonAmount', 0), decimals: 2"></input>
					<br>
					<label for="PaidBack" class="control-label">Paid Back:</label>
					<input type="text" class="input-sm input-smaller form-control form-control-lite" name="PaidBack" id="PaidBack" data-bind="value: _.result(dispute(), 'PaidBack', 0), number: _.result(dispute(), 'PaidBack', 0), decimals: 2"></input>
					
					<br>
					<label for="resolutionDt" class="control-label">Resolution Date:</label>
					<input type="text" class="input-sm input-smaller form-control form-control-lite" name="ResolutionDt" id="ResolutionDt" data-bind="value: dispute().ResolutionDt ? moment(dispute().ResolutionDt).format('MM/DD/YYYY') : ''"></input>
					<br>
					<label for="disputeNotes" class="control-label">Notes:</label>
					<input type="text" class="input-sm input-smaller form-control form-control-lite" size="75" name="DisputeNotes" id="DisputeNotes" data-bind="value: dispute().DisputeNotes"></input>
					<br>
					<label for="remedyTicket" class="control-label">Remedy Ticket:</label>
					<input type="text" class="input-sm input-smaller form-control form-control-lite" name="RemedyTicket" id="RemedyTicket" data-bind="value: dispute().RemedyTicket"></input>
					<br>
					<a data-bind="if: dispute().RemedyTicket, attr: { href: '{{ Config::get('constants.props.remedyLink')}}' + $root.dispute().RemedyTicket }" target="_new">Remedy Link</a>
				</div>
				<a data-bind="attr: { href: 'disputes?accountNum=' + escape($root.accountNum()) + '&circuit=' + escape(dispute().CircuitId) + '&inv=' + dispute().inv}, text: 'Jump to disputes page for this account/circuit'" target='_disputes'></a><br>
				<!-- ko if: dispute().old -->
				<hr class="less-margin">
				<label class="control-label">Charges with Remaining > 0:</label><br>
				<table class="table-borderless table-condensed">
						<tr class="table-tiny">
							<td><label for="amount"  >InvoiceDt</label></td>
							<td><label for="disputed" >Remaining</label></td>
							<td><label for="type" >Type</label></td>
							<td><label for="remaining" >Dispute Status</label></td>
						</tr>
						
						
					
				<!-- ko foreach: dispute().old -->
				<tr>
							<td><span data-bind="text: $data.InvoiceDt ? moment($data.InvoiceDt).format('MM/DD/YYYY') : ''"></span></td>
							<td><span data-bind="text: $data.Remaining"></span></td>
							<td><span data-bind="text: $data.Type"></span></td>
							<td><span data-bind="text: $data.Status"></span></td>
						</tr>
				<!-- /ko -->
				</table>
				<!-- /ko -->
				

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary" >Save Changes</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
