<?php

	$column_width = (int)(90/count($columns));

	if(!empty($list)){
?><div class="" >
		<table class="table table-bordered" id="flex1">
		<thead>
			<tr class=''>
				<?php if(!$unset_delete || !$unset_edit || !$unset_read || !$unset_clone || !empty($actions)){?>
				<th align="left" abbr="tools" axis="col1" class="10%">
						<?php echo $this->l('list_actions'); ?>
				</th>
				<?php }?>
				<?php foreach($columns as $column){?>
				<th width='<?php echo $column_width?>%'>
					<div class="field-sorting <?php if(isset($order_by[0]) &&  $column->field_name == $order_by[0]){?><?php echo $order_by[1]?><?php }?>"
						rel='<?php echo $column->field_name?>'>
						<?php echo $column->display_as?>
					</div>
				</th>
				<?php }?>

			</tr>
		</thead>
		<tbody>
<?php foreach($list as $num_row => $row){ ?>
		<tr  <?php if($num_row % 2 == 1){?>class="erow"<?php }?>>

			<?php if(!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)){?>
			<td align="left" width=''>
				<div class='tools'>
					<div class="btn-group">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<!-- 기능 -->
							<i class="fa fa-gear"></i>
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">


					<?php if(!$unset_delete){?>
						<li>
                    	<a href='<?php echo $row->delete_url?>' title='<?php echo $this->l('list_delete')?> <?php echo $subject?>' class="delete-row" >
                    		<i class="fa fa-remove"></i> <?=$this->l('list_delete')?>
                    	</a>
                    	</li>
                    <?php }?>
                    <?php if(!$unset_clone){?>
                    	<li>
                        <a href='<?php echo $row->clone_url?>' title='<?php echo $this->l('list_clone')?> <?php echo $subject?>' class="clone_button">
							<i class="fa fa-copy"></i> <?=$this->l('list_clone')?>
                        </a>
                        </li>
                    <?php }?>
					<?php if(!$unset_read){?>
						<li>
						<a href='<?php echo $row->read_url?>' title='<?php echo $this->l('list_view')?> <?php echo $subject?>' class="edit_button">
							<i class="fa fa-file-o"></i> <?=$this->l('list_view')?>
						</a>
						</li>
					<?php }?>

					<?php
					if(!empty($row->action_urls)){
						foreach($row->action_urls as $action_unique_id => $action_url){
							$action = $actions[$action_unique_id];
					?>
							<li>
							<a href="<?php echo $action_url; ?>" class="<?php echo $action->css_class; ?> crud-action" title="<?php echo $action->label?>"><?php
								if(!empty($action->image_url))
								{
									?>
									<i class="<?php echo $action->image_url; ?>"></i> <?php echo $action->label?>
									<?php
								}
							?></a>
							</li>
					<?php }
					}
					?>

						</ul>

					</div>
                    <?php if(!$unset_edit){?>
						<a href='<?php echo $row->edit_url?>'
							title='<?php echo $this->l('list_edit')?> <?php echo $subject?>'
							class="edit_button btn btn-info"
							>
							<!-- <i class="fa fa-pencil"></i>  -->
							<?=$this->l('list_edit')?>
						</a>
					<?php }?>
                    <div class='clear'></div>
				</div>
			</td>
			<?php }?>
			<?php foreach($columns as $column){?>
			<td width='<?php echo $column_width?>%' class='<?php if(isset($order_by[0]) &&  $column->field_name == $order_by[0]){?>sorted<?php }?>'>
				<div class='text-left'><?php echo $row->{$column->field_name} != '' ? $row->{$column->field_name} : '&nbsp;' ; ?></div>
			</td>
			<?php }?>

		</tr>
<?php } ?>
		</tbody>
		</table>
	</div>
<?php }else{?>
	<br/>
	&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $this->l('list_no_items'); ?>
	<br/>
	<br/>
<?php }?>
