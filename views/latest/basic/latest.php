<? $this->assets->load_css(VIEWFOLDER."latest/{$skin}/latest.less"); ?>

<div class="panel panel-default latest latest_basic">
	<div class="panel-heading"><?=$title?></div>
	<div class="panel-body">
		<ul class="latest">
			<? foreach( (array) $list as $key => $row ) : ?>
				<li>
					<a href="<?=$base_link?>view/<?=$row->post_id?>">
						<p class="title">
							<?=$row->post_title?>
						</p>
					</a>
					<div class="desc">
						<p class="date">
							<?=get_datetime($row->post_created_at)?>
						</p>
						<p class="writer">
							<?=writer_display($row)?>
						</p>
					</div>
				</li>
			<? endforeach; ?>
		</ul>
	</div>
</div>

<? $this->assets->load_js(VIEWFOLDER."latest/{$skin}/latest.js"); ?>
