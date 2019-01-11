<? $this->assets->load_css(VIEWDIR."latest/{$skin}/latest.less"); ?>

<div class="panel panel-primary panel-bordered latest latest_basic">
	<div class="panel-heading">
		<h3 class="panel-title"><?=$title?></h3>
	</div>
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
							<?=get_date($row->post_created_at)?>
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

<? $this->assets->load_js(VIEWDIR."latest/{$skin}/latest.js"); ?>
