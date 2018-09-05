<?php defined('BASEPATH') OR exit('No direct script access allowed');


class File_model extends MY_Model {
	public $prefix = 'file_';

	public function __construct()
	{
		parent::__construct();

		$this->protected[] = $this->primary_key;
		$this->created = true;

		$this->after_get[] = 'after_get';
		$this->after_delete[] = 'after_delete';

		// before_create
		// after_create
		// before_update
		// after_update
		// before_get
		// after_get
		// before_delete
		// after_delete

	}


	public function post_image($post_id) {
		return $this
				->where('file_rel_type', 'board')
				->where('file_rel_id', $post_id)
				->where('file_is_image', 1)
				->order_by('file_id', 'ASC')
				->get();
	}

	public function member_image($mb_id) {
		return $this
				->where('file_rel_type', 'member')
				->where('file_rel_desc', 'photo')
				->where('file_rel_id', $mb_id)
				->get();
	}

	public function thumb_delete( $file ) {
		$thumb_folder = $this->config->item('file_path_php').'thumb/';
		if( is_dir( $thumb_folder ) ) {
			$files = glob($thumb_folder."*{$file->file_save}*");
			foreach( $files as $key => $row ) :
				@unlink( $row );
			endforeach;
		}
	}

	public function file_delete( $file ) {
		$file = (object)$file;

        $real_folder = $this->config->item('file_path_php').$file->file_folder;
        $file_path = $real_folder.$file->file_save;

        @chmod($real_folder, DIR_WRITE_MODE);

        // 파일삭제
        if( !@unlink( $file_path ) ) {
            log_message('error',
                'File_model / file_delete() 삭제불가'.PHP_EOL
                ."::: 파일 아이디 : {$file->file_id}".PHP_EOL
                ."::: 파일 명 : {$file_path}"
            );
        }

        $this->thumb_delete($file);
	}

	// 옵저버

	public function after_get($data) {
		if( $data->file_save )
			$data->web_path = $this->config->item('file_path').$data->file_folder.$data->file_save;

		return $data;
	}

	public function after_delete( $data ) {
		$this->kmh->log($data, '파일삭제');
		foreach( fe( $data['data'] ) as $key => $file ) :
			$this->file_delete( $file );
		endforeach;
	}
}
