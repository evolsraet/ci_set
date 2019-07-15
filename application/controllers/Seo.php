<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use samdark\sitemap\Sitemap;
use samdark\sitemap\Index;

class Seo extends MY_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function test() {
		$this->load->library('user_agent');
		die( "브라우저 : {$this->agent->browser()}" );
	}

	public function rss() {
		$this->data['feeds'] = array();

		// 타브
			$this->load->model('tab/tab_model');
			$tabs = $this->tab_model
						->where('tab_public', 1)
						->order_by('tab_updated_at', 'desc')
						->get_all();
			foreach( (array) $tabs as $key => $row ) :
				$feed              = new stdClass;
				$feed->title       = "{$row->song_name} by {$row->artist_name} 코드 악보 - {$this->config->item('site_title')}";
				$feed->url         = base_url() . "tab/view/{$row->tab_id}";
				$feed->description = $row->tab_detail;
				$feed->category    = "코드북";
				$feed->author      = $row->mb_display;
				$feed->pubdate     = date( DATE_RFC822, strtotime($row->tab_updated_at) );
				$feed->updated_at  = strtotime($row->tab_updated_at);

				$this->data['feeds'][] = $feed;
			endforeach;

		// 게시판
			$this->load->model('post_model');

			$posts = $this->post_model
							->join('board', 'board_id = post_board_id')
							->join('member', 'mb_id = post_mb_id', 'left outer')
							->where('post_deleted_at', null)
							->order_by('post_updated_at', 'desc')
							->get_all();

			foreach( (array) $posts as $key => $row ) :
				$feed              = new stdClass;
				$feed->title       = "{$row->post_title} > {$row->board_name} - {$this->config->item('site_title')}";
				$feed->url         = base_url() . "board/{$row->board_id}/view/{$row->post_id}";
				$feed->description = $row->post_content;
				$feed->category    = $row->board_name;
				$feed->author      = $row->mb_display;
				$feed->pubdate     = date( DATE_RFC822, strtotime($row->post_updated_at) );
				$feed->updated_at  = strtotime($row->post_updated_at);

				$this->data['feeds'][] = $feed;
			endforeach;

		// 정렬
			$sort = array();
			foreach ((array) $this->data['feeds'] as $key => $row) {
			    $sort[$key] = $row->updated_at;
			}
			
			if( count($sort) ) :
				array_multisort($sort, SORT_DESC, $this->data['feeds']);
				$this->data['lastBuildDate'] = $this->data['feeds'][0]->pubdate; // 채널 데이터
			endif;

		// kmh_print($this->data['feeds']);
		// die();

		// 출력
		// header("Content-Type: application/rss+xml");
		header("Content-Type: application/xml");
		$this->load->view('pages/rss', $this->data);
	}

	public function sitemap() {
		// 네비 메뉴 쿠가 후 필요한 게시물 등록
		$domain = base_url();

		$sitemap_path_php = $this->config->item('file_path_php') . 'sitemap';
		$sitemap_path = $this->config->item('file_path') . 'sitemap';

		mkdir_path( $sitemap_path_php );

		// create sitemap
		$sitemap = new Sitemap($sitemap_path_php. '/sitemap.xml');

		// 동적 주소
			// 타브
				$this->load->model('tab/tab_model');
				$tabs = $this->tab_model
							->where('tab_public', 1)
							->order_by('tab_updated_at', 'desc')
							->get_all();
				foreach( (array) $tabs as $key => $row ) :
					$sitemap->addItem("{$domain}tab/view/{$row->tab_id}", strtotime($row->tab_updated_at) );
				endforeach;

			// 게시판
				$this->load->model('post_model');

				$posts = $this->post_model
								->select('board.board_base_url, post.post_id, post.post_updated_at')
								->join('board', 'board.board_id = post.post_board_id')
								->where('post_deleted_at', null)
								->order_by('post_updated_at', 'desc')
								->get_all();

				foreach( (array) $posts as $key => $row ) :
					$url = "{$domain}{$row->board_base_url}/view/{$row->post_id}";
					$sitemap->addItem(
						$url,
						strtotime($row->post_updated_at)
					);
				endforeach;

		// write it
		$sitemap->write();

		// get URLs of sitemaps written
		$sitemapFileUrls = $sitemap->getSitemapUrls( base_url( $sitemap_path . '/' ) );

		// create sitemap for static files
		$staticSitemap = new Sitemap($sitemap_path_php . '/sitemap_static.xml');

		// 정적 주소
			$nav = $this->config->item('nav');
			$nav_sub = $this->config->item('nav_sub');

			// 메인
			$staticSitemap->addItem("{$domain}", time());

			// 네비
			foreach( (array) $nav as $nav_key => $nav_row ) :
				$exlude = array(
					'member'
				);
				if( in_array($nav_key, $exlude) )
					continue;

				foreach( (array) $nav_sub[$nav_key] as $sub_key => $sub_row ) :
					$url = "{$domain}{$nav_key}/" . ignore($sub_key);
					$staticSitemap->addItem($url, time());
				endforeach;
			endforeach;

		// write it
		$staticSitemap->write();

		// get URLs of sitemaps written
		$staticSitemapUrls = $staticSitemap->getSitemapUrls( base_url( $sitemap_path . '/' ) );

		// create sitemap index file
		$index = new Index($sitemap_path_php . '/sitemap_index.xml');

		// add URLs
		foreach ($sitemapFileUrls as $sitemapUrl) {
		    $index->addSitemap($sitemapUrl);
		}

		// add more URLs
		foreach ($staticSitemapUrls as $sitemapUrl) {
		    $index->addSitemap($sitemapUrl);
		}

		// write it
		$index->write();

		// 출력
		$this->load->helper('file');
		$path = $sitemap_path_php . '/sitemap_index.xml';
		$xml = read_file($path);

		header('Content-type: text/xml');
		echo $xml;
	}
}
