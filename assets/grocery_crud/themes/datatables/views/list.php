<table cellpadding="0" cellspacing="0" border="0" class="display groceryCrudTable" id="<?php echo uniqid(); ?>">
	<thead>
		<tr>
			<?php foreach($columns as $column){?>
				<th><?php echo $column->display_as; ?></th>
			<?php }?>
			<?php if(!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)){?>
			<th class='actions'><?php echo $this->l('list_actions'); ?></th>
			<?php }?>
		</tr>
	</thead>
	<tbody>
		<?php foreach($list as $num_row => $row){ ?>
		<tr id='row-<?php echo $num_row?>'>
			<?php foreach($columns as $column){?>
				<td><?php echo $row->{$column->field_name}?></td>
			<?php }?>
			<?php if(!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)){?>
			<td class='actions'>

				<div class="btn-group">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						기능 <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<?php
						if(!empty($row->action_urls)){
							foreach($row->action_urls as $action_unique_id => $action_url){
								$action = $actions[$action_unique_id];
						?>
							<li>
								<a href="<?php echo $action_url; ?>" class="" role="button">
									<!-- <span class="ui-button-icon-primary ui-icon <?php echo $action->css_class; ?> <?php echo $action_unique_id;?>"></span> -->
									<span class="">&nbsp;<?php echo $action->label?></span>
								</a>
							</li>
						<?php }
						}
						?>
						<?php if(!$unset_read){?>
							<li>
							<a href="<?php echo $row->read_url?>" class="" role="button">
								<!-- <span class="ui-button-icon-primary ui-icon ui-icon-document"></span> -->
								<span class="">&nbsp;<?php echo $this->l('list_view'); ?></span>
							</a>
							</li>
						<?php }?>

		                <?php if(!$unset_clone){?>
		                	<li>
		                    <a href="<?php echo $row->clone_url?>" class="" role="button">
		                        <!-- <span class="ui-button-icon-primary ui-icon ui-icon-copy"></span> -->
		                        <span class="">&nbsp;<?php echo $this->l('list_clone'); ?></span>
		                    </a>
		                	</li>
		                <?php }?>

						<?php if(!$unset_edit){?>
							<li>
							<a href="<?php echo $row->edit_url?>" class="" role="button">
								<!-- <span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span> -->
								<span class="">&nbsp;<?php echo $this->l('list_edit'); ?></span>
							</a>
							</li>
						<?php }?>

						<?php if(!$unset_delete){?>
							<li>
							<a onclick = "javascript: return delete_row('<?php echo $row->delete_url?>', '<?php echo $num_row?>')"
								href="javascript:void(0)" class="" role="button">
								<!-- <span class="ui-button-icon-primary ui-icon ui-icon-circle-minus"></span> -->
								<span class="">&nbsp;<?php echo $this->l('list_delete'); ?></span>
							</a>
							</li>
						<?php }?>
					</ul>
				</div>
			</td>
			<?php }?>
		</tr>
		<?php }?>
	</tbody>
	<tfoot>
		<tr>
			<?php foreach($columns as $column){?>
				<th><input type="text" name="<?php echo $column->field_name; ?>" placeholder="<?php echo $this->l('list_search').' '.$column->display_as; ?>" class="search_<?php echo $column->field_name; ?>" /></th>
			<?php }?>
			<?php if(!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)){?>
				<th>
					<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only floatR refresh-data" role="button" data-url="<?php echo $ajax_list_url; ?>">
						<span class="ui-button-icon-primary ui-icon ui-icon-refresh"></span><span class="">&nbsp;</span>
					</button>
					<a href="javascript:void(0)" role="button" class="clear-filtering ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary floatR">
						<span class="ui-button-icon-primary ui-icon ui-icon-arrowrefresh-1-e"></span>
						<span class=""><?php echo $this->l('list_clear_filtering');?></span>
					</a>
				</th>
			<?php }?>
		</tr>
	</tfoot>
</table>
