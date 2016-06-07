<div class="row">
	<form action="/{{ $goBackRoutePath }}" method="get" id="searchBox" data-no-api-prefix="1" data-bind="submit: searchBox.bind($root)">
		<div class="col-md-2 form-group">
			<label class="control-label" for="vendor">Vendor:</label>
			<input type="text" name="vendor" id="vendor" class="form-control input-sm typeahead" data-bind="value: vendorId"/>
		</div>

		<div class="col-md-2 form-group">
			<label class="control-label" for="circuit">CircuitId:</label>
			<input type="text" name="circuit" id="circuit" class="form-control input-sm typeahead" data-bind="value: circuit">
		</div>
		
		<div class="col-md-2 form-group">
			<label class="control-label" for="pon">PON:</label>
			<input type="text" name="pon" class="form-control input-sm" data-bind="value: pon">
		</div>
		
		<div class="col-md-2 form-group">
			<label class="control-label" for="accountNumQuery">Account Number:</label>
			<input type="text" name="accountNumQuery" id="accountNumQuery" class="form-control input-sm typeahead" data-bind="value: accountNumQuery">
		</div>

		<div class="col-md-4 form-group">
			<label class="control-label show">&nbsp;</label>
			<button type="submit" class="btn btn-sm btn-primary">
				<i class="fa fa-spinner fa-pulse" data-bind="visible: isLoading"></i>
				Go
			</button>
		</div>
	</form>
</div>

<div class="panel panel-default panel-group">
	<span data-bind="if: vendorDesc"><span data-bind="text: vendorDesc"></span></span><span data-bind="ifnot: vendorDesc">&nbsp;</span>
	<span data-bind="if: region"><br><span data-bind="text: 'Region - ' + region()"></span></span><span data-bind="ifnot: region">&nbsp;</span>
</div>

    
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
		<li id="invoicesLi" >
          <a href="#invoices" role="tab" data-toggle="tab" id="invoicesTabEntry">
              Invoices
          </a>
      </li>
      <li id="accountsLi" class="active">
		  <a href="#accounts" role="tab" data-toggle="tab" id="accountTabEntry">
			Accounts
          </a>
      </li>
    </ul>


<div class="tab-content">
	<div class="tab-pane fade panel panel-default panel-group " id="invoices">
		<span><h4 data-bind='text: accountNum'>
		
		</h4>
		<!-- ko if: $root.accountNum -->
				<button type="button" class="btn btn-sm btn-primary" data-bind="click: $root.newInvoiceHeader.bind($root)">
					<i class="fa fa-spinner fa-pulse" data-bind="visible: isLoading"></i>
					Create New Invoice
				</button>
				<!-- /ko -->
		</span>
		<table class="table table-striped table-responsive table-condensed small" id="invoiceTable">
			<thead>
				<tr>
					<th >Invoice #</th>
					<th >InvoiceDt</th>
					<th >Invoice Total</th>
					<th >Accepted Total</th>
					<th >Remaining</th>
					<th >Status</th>
					<th >GP #</th>
					<th >Last Updated</th>
					<th >Last Updated By</th>
				</tr>
			</thead>
			
			<tbody data-bind="visible: invoices().length == 0">
			<td>No Invoices Found</td>
			</tbody>
			<tbody data-bind="foreach: invoices" data-bind="visible: invoices().length > 0">
				<tr>
					<td>
						<a data-bind="attr: { href: '{{ $goBackRoutePath }}', title: $data.InvoiceNum }, text: $data.InvoiceNum, click: $root.viewInvoice.bind($root)"></a>
						<!--<span data-bind="text: $data.invoiceNum"></span>-->
					</td>
					<td><span data-bind="text: moment($data.InvoiceDt).format('MM/DD/YYYY')"></span></td>
					<td class="text-right"><span data-bind="currency: $data.InvoiceTotal, decimals: 2"></span></td>
					<td class="text-right"><span data-bind="currency: $data.AcceptedTotal, decimals: 2"></span></td>
					<td class="text-right"><span data-bind="currency: $data.RemainingOpenDispute, decimals: 2"></span></td>
					<td><span data-bind="text: $data.invoiceStatusDesc"></span></td>
					<td><span data-bind="text: $data.GPInvoiceNum"></span></td>
					<td><span data-bind="text: moment($data.LastUpDt).format('MM/DD/YYYY')"></span></td>
					<td><span data-bind="text: $data.LastUpUser"></span></td>
				</tr>
			</tbody>
		</table>
		<div class="nav nav-justified" data-bind="visible: invoicesHasPrevPage() || invoicesHasNextPage()">
			
			
				<ul class="pager">
					<li data-bind="css: { disabled: !invoicesHasPrevPage() }"><a href="#" data-bind="click: invoicesPageChange.bind($root, -1), attr: { disabled: !invoicesHasPrevPage() }">Previous</a></li>
					<li data-bind="css: { disabled: !invoicesHasNextPage() }"><a href="#" data-bind="click: invoicesPageChange.bind($root, 1), attr: { disabled: !invoicesHasNextPage() }">Next</a></li>
				</ul>
			
		</div>
	 </div>

	<div class="tab-pane fade active in panel panel-default panel-group" id="accounts">
		<table class="table table-striped table-responsive table-condensed small" id="accountsTable">
			<thead>
				<tr>
					<th>Account</th>
					<th>Latest Invoice Date</th>
				</tr>
			</thead>
			
			<tbody data-bind="visible: $root.accounts().length == 0 && $root.searched()">
				<td colspan="2">No Accounts Found</td>
			</tbody>

			<tbody data-bind="foreach: accounts" data-bind="visible: accounts().length > 0">
				<tr>
					<td>
						<a data-bind="attr: { href: '{{ $goBackRoutePath }}', title: $data.TelcoAccNum }, text: $data.TelcoAccNum, click: $root.viewAccount.bind($root)"></a>
					</td>
					<td>
						<!-- ko if: $data.latestInvoice --><span data-bind="text: moment($data.latestInvoice.InvoiceDt).calendar()"></span><!-- /ko -->
					</td>
				</tr>
			</tbody>
	
		</table>
		<div class="nav nav-justified" data-bind="visible: accountsHasPrevPage() || accountsHasNextPage()">
			
			
				<ul class="pager">
					<li data-bind="css: { disabled: !accountsHasPrevPage() }"><a href="#" data-bind="click: accountsPageChange.bind($root, -1), attr: { disabled: !accountsHasPrevPage() }">Previous</a></li>
					<li data-bind="css: { disabled: !accountsHasNextPage() }"><a href="#" data-bind="click: accountsPageChange.bind($root, 1), attr: { disabled: !accountsHasNextPage() }">Next</a></li>
				</ul>
			
		</div>
	</div>
</div>