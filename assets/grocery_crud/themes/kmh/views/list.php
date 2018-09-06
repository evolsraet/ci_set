<table cellpadding="0" cellspacing="0" border="0" class="display groceryCrudTable" id="<?php echo uniqid(); ?>">
	<thead>
		<tr>
			<? if (!$unset_delete) : ?>
				<th class="actions text-center">
	                <input type="checkbox" class="select-all-row"> 선택
		        </th>		
		    <? endif; ?>		
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
			<? if (!$unset_delete) : ?>
				<td class="text-center">
	                <input type="checkbox" class="select-row" data-id="<?php echo $row->primary_key_value; ?>">
		        </td>		
		    <? endif; ?>
			<?php foreach($columns as $column){?>
				<td><?php echo $row->{$column->field_name}?></td>
			<?php }?>
			<?php if(!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)){?>
			<td class='actions'>
				<?php
				if(!empty($row->action_urls)){
					foreach($row->action_urls as $action_unique_id => $action_url){
						$action = $actions[$action_unique_id];
				?>
						<a href="<?php echo $action_url; ?>" class="edit_button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
							<span class="ui-button-icon-primary ui-icon <?php echo $action->css_class; ?> <?php echo $action_unique_id;?>"></span><span class="ui-button-text">&nbsp;<?php echo $action->label?></span>
						</a>
				<?php }
				}
				?>
				<?php if(!$unset_read){?>
					<a href="<?php echo $row->read_url?>" class="edit_button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
						<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
						<span class="ui-button-text">&nbsp;<?php echo $this->l('list_view'); ?></span>
					</a>
				<?php }?>

				<?php if(!$unset_edit){?>
					<a href="<?php echo $row->edit_url?>" class="edit_button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
						<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
						<span class="ui-button-text">&nbsp;<?php echo $this->l('list_edit'); ?></span>
					</a>
				<?php }?>
				<?php if(!$unset_edit){?>
					<a href="<?php echo $row->edit_url?>" class="btn btn-warning" role="button">
						<i class="icon-gear"></i>
						<span class="ui-button-text">&nbsp;<?php echo $this->l('list_edit'); ?></span>
					</a>
				<?php }?>				
				<?php if(!$unset_delete){?>
					<a onclick = "javascript: return delete_row('<?php echo $row->delete_url?>', '<?php echo $num_row?>')"
						href="javascript:void(0)" class="delete_button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
						<span class="ui-button-icon-primary ui-icon ui-icon-circle-minus"></span>
						<span class="ui-button-text">&nbsp;<?php echo $this->l('list_delete'); ?></span>
					</a>
				<?php }?>
			</td>
			<?php }?>
		</tr>
		<?php }?>
	</tbody>
	<tfoot>
		<tr>
			<? if (!$unset_delete) : ?>
				<th>
	                <button type="button" class="batch_delete btn">선택삭제</button>
		        </th>		
		    <? endif; ?>		
			<?php foreach($columns as $column){?>
				<th><input type="text" name="<?php echo $column->field_name; ?>" placeholder="<?php echo $this->l('list_search').' '.$column->display_as; ?>" class="search_<?php echo $column->field_name; ?>" /></th>
			<?php }?>
			<?php if(!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)){?>
				<th>
					<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only floatR refresh-data" role="button" data-url="<?php echo $ajax_list_url; ?>">
						<span class="ui-button-icon-primary ui-icon ui-icon-refresh"></span><span class="ui-button-text">&nbsp;</span>
					</button>
					<a href="javascript:void(0)" role="button" class="clear-filtering ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary floatR">
						<span class="ui-button-icon-primary ui-icon ui-icon-arrowrefresh-1-e"></span>
						<span class="ui-button-text"><?php echo $this->l('list_clear_filtering');?></span>
					</a>
				</th>
			<?php }?>
		</tr>
	</tfoot>
</table>

<script>
	$(document).ready(function() {
		$(".select-all-row").click(function(event) {
			if( $(this).attr('data-toggle')=='has' ) {
				$(".select-row").prop('checked',false);
				$(this).attr('data-toggle','');
			} else {
				$(".select-row").prop('checked',true);
				$(this).attr('data-toggle','has');
			}
		});

		$(".batch_delete").click(function(event) {
			var select_ids = '';
			$(".select-row").each(function(index, el) {
				if( $(this).prop('checked')==true )
					select_ids += $(this).attr('data-id')+"|";
			});

			if(select_ids=='') {
				alert("일괄삭제할 항목의 체크 박스를 선택하세요.");
				return false;
			}

			if( !confirm('선택한 항목들을 삭제하시겠습니까?') ) return false;
			if( !confirm("DB에서 삭제합니다. 관련된 항목은 모두 삭제됩니다.\n정말 삭제하시겠습니까?") ) return false;

			url = '/admin/batch_delete';
			btn = $(this);

			$.ajax({
				url: url,
				type: 'post',
				dataType: 'json',
				data: { 
						'csrftestname' : $("#tkey").attr('data-thash'),
						'table' : $("#tkey").attr('data-tname'),
						'select_ids' : select_ids
					},
				beforeSend : function() {
					$(btn).button('loading');
				},
				complete : function() {
					$(btn).button('reset');
				},
				error : function(request ,status, error) {
					alert('AJAX 통신 중 에러가 발생했습니다.');
					console.log( request.responseText );
				},
				success : function(response, status, request) {
					if( response.status == 'ok' ) {
						location.reload();
					} else {
						alert(response.msg);
					}
				}
			});	
		});
	});
</script>