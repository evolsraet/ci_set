<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_board_config extends CI_Migration {

	public function __construct()
	{
		parent::__construct();
		$this->load->dbforge();
	}


	public function up()
	{
		$this->dbforge->add_field(array(
			'blog_id' => array(
				'type' => 'INT',
				'constraint' => 5,
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			),
			'blog_title' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
			),
			'blog_description' => array(
				'type' => 'TEXT',
				'null' => TRUE,
			),
		));
		$this->dbforge->add_key('blog_id', TRUE);
		$this->dbforge->create_table('blog');
	}

	public function down()
	{
		$this->dbforge->drop_table('blog');
	}
}