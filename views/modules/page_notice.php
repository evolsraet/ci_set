<? foreach( (array) $this->session->flashdata('page_notice') as $key => $row ) : ?>
	<?
		$class = $row['class']!='' ? $row['class'] : 'success';
	?>
	<div class="alert alert-<?=$class?> text-center alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<?=$row['msg']?>
	</div>
<? endforeach; ?>