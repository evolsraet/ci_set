	<div class="text-center">
		<?
			$file = TIMG.'page/'.$this->uri->segment(1).'_'.$this->uri->segment(2).'.png';
		?>
		<? if ( file_exists('.'.$file) ) : ?>
			<img src="<?=$file?>">
		<? else : ?>
			<img src="<?=IMG?>common/waiting.png" alt="준비중입니다.">
		<? endif; ?>
	</div>