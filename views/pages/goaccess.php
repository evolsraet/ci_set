<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title></title>
	<link rel="stylesheet" href="https://unpkg.com/tableexport/dist/css/tableexport.min.css">

	<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.14.1/xlsx.core.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.min.js"></script>
	<script src="https://unpkg.com/tableexport/dist/js/tableexport.min.js"></script>
</head>
<body>

	<? foreach( (array) $tables as $key => $row ) : ?>
		<div id="wrap_<?=$key?>">
			<h1>
				<?=$row['title']?>
			</h1>
			<?=$row['table']?>

			<button type="button" class="toXSL">엑셀</button>
			<script>
				$("#wrap_<?=$key?> .toXSL").click(function(event) {
					$("#wrap_<?=$key?> table").tableExport({
						fileName: "<?=$key?>",
					});
				});
			</script>
		</div>
	<? endforeach; ?>


</body>
</html>