

<table style="display: none" class="table table-responsive table-condensed small" id="dataTable">
	<thead>
		<tr>
			<th>AccountNum</th>
			<th>CircuitId</th>
			<th>PON</th>
			<th>VendorId</th>
			<th>InvoiceDt</th>
			<th>Billed</th>
			<th>CircuitMRC</th>
			<th>MRCDifference</th>
		</tr>
	</thead>
	<tbody data-bind="foreach: linesAndPons">
		<tr>	
			<td><span data-bind="text: $root.accountNum"></span></td>
			<td><span data-bind="text: $data.telcoOrderDetails.CircuitID"></span></td>
			<td><span data-bind="text: $data.telcoOrderDetails.PON"></span></td>
			<td><span data-bind="text: $root.vendorId"></span></td>
			<td><span data-bind="text: $root.currentInvoice().InvoiceDt"></span></td>
			<td><span data-bind="text: $data.billedSum()"></span></td>
			<td><span data-bind="text: $data.telcoOrderDetails.MRC"></span></td>
			<td><span data-bind="text: $data.billedSum() - $data.telcoOrderDetails.MRC"></span></td>
			
		</tr>
			</tbody>
			
</table>
	
