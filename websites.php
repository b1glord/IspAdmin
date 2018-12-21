<?php
require_once 'config.inc.php';
require_once 'functions.inc.php';

$websites = IspGetActiveWebsites ();

$data = [];
foreach ($websites as $i => $domain) {
	$data[$i] = [
		'domain' => $domain,
	];
}


// fork processes to query sslExpires simultaneously
foreach ($websites as $i => $domain) {
	$pipe[$i] = popen('php ./sslExpires.php ' . $domain, 'r');
}

// wait for them to finish
foreach ($websites as $i => $domain) {
	$data[$i] = [
		'domain' => $domain,
		'sslExpires' => datestring_parse (fgets ($pipe[$i])),
	];
	pclose($pipe[$i]);
}

// sort table by sslExpires
sort2dArray ($data, 'sslExpires', true);

?>
<html>
	<head>
		<title>websites</title>
	</head>
	<body>
		<table>
			<thead>
				<tr>
					<th>Domain</th> <th>SSL</th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ($data as $row) {
			?>
				<tr>
					<td><?= $row['domain'] ?></td> <td><?= datetime_format ($row['sslExpires']) ?></td>
				</tr>
			<?php
			}
			?>
			</tbody>
		</table>
	</body>
</html>
