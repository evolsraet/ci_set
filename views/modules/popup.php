<?
	$now_date = date('Y-m-d');
	$popups = $this->db
		->where('pu_start <=', $now_date)
		->where('pu_end >=', $now_date)
		->get('popup')
		->result();

	// kmh_print( $popups );
?>

<? foreach( fe($popups) as $row ) : ?>
	<? if ( $_COOKIE['popup_'.$row->pu_id]!='yes' ) : ?>
		<? if ( $row->pu_background=='' ) : ?>
			<? $row->pu_background = "#000"; ?>
		<? endif; ?>
	<style type="text/css">
		#popup_<?=$row->pu_id?> {
			margin: 0;
			background: <?=$row->pu_background?>;
			text-align: <?=$row->pu_align?>;
			<? if ( $row->pu_type=='일반' ) : ?>
			<? else : ?>
				position: absolute;
				left: <?=$row->pu_x?>px;
				top: <?=$row->pu_y?>px;
				width: <?=$row->pu_width?>px;
				height: <?=$row->pu_height?>px;
				z-index: <?=1000+$row->pu_id?>;
				border: 2px solid #000;
			<? endif; ?>
		}
		.close_btn {
		    text-align: center;
		    padding: 4px 0;
		    background: #000;
		}
	</style>
	<div id="popup_<?=$row->pu_id?>" class="popup <?=$row->pu_type=="레이어"?"layer":""?>">
		<? if( $row->pu_link ) : ?>
		<a href="<?=$row->pu_link?>" title="<?=$row->pu_title?>">
		<? endif; ?>
			<img src="<?=$this->config->item('file_path')?>crud/<?=$row->pu_file?>" alt="팝업이미지">
			<? if( trim(strip_tags($row->pu_desc))!='' ) : ?>
				<?=$row->pu_desc?>
			<? endif; ?>			
		<? if( $row->pu_link ) : ?>
		</a>
		<? endif; ?>
		<div class="close_btn">
			<a href="javascript:never_popup(<?=$row->pu_id?>)" style="color: #fff !important;">일주일간 보지 않기</a>
			<a href="javascript:browser_popup(<?=$row->pu_id?>)" style="color: #fff !important; font-weight: 700">[닫기]</a>
		</div>
	</div>
	<? endif; ?>
<? endforeach; ?>