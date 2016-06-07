<!doctype html>
<html>
	
	<head>
		<title>Customers</title>
		<link rel='stylesheet' type='text/css' href='/build/cogent.css'>
		
	</head>	

	<body>
<table class="table table-striped table-responsive table-condensed small" id="dataTable">
	<thead>
		<?php
			$first = $set[0];
		?>
		@foreach ($first as $key => $value)
		<th>
			{{ $key }}
		</th>
		@endforeach
	</thead>
	<tbody>
		@foreach ($set as $row)
		<tr>

			@foreach ($row as $key => $value)
			<td>
				{{ $value }}
			</td>
			@endforeach
		</tr>
		@endforeach
	</tbody>
</table>

</body>
</html>