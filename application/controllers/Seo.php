<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use samdark\sitemap\Sitemap;
use samdark\sitemap\Index;

class Seo extends MY_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->sitemap();
	}

	public function sitemap() {
		// 네비 메뉴 쿠가 후 필요한 게시물 등록
		$domain = 'http://'.$_SERVER['HTTP_HOST'].'/';

		$sitemap_path_php = $this->config->item('file_path_php') . 'sitemap';
		$sitemap_path = $this->config->item('file_path') . 'sitemap';

		mkdir_path( $sitemap_path_php );

		// create sitemap
		$sitemap = new Sitemap($sitemap_path_php. '/sitemap.xml');

		// add some URLs
		$sitemap->addItem("{$domain}mylink1");
		$sitemap->addItem("{$domain}mylink2", time());
		$sitemap->addItem("{$domain}mylink3", time(), Sitemap::HOURLY);
		$sitemap->addItem("{$domain}mylink4", time(), Sitemap::DAILY, 0.3);

		// write it
		$sitemap->write();

		// get URLs of sitemaps written
		$sitemapFileUrls = $sitemap->getSitemapUrls("http://{$domain}");

		// create sitemap for static files
		$staticSitemap = new Sitemap($sitemap_path_php . '/sitemap_static.xml');

		// add some URLs
		$staticSitemap->addItem("{$domain}about");
		$staticSitemap->addItem("{$domain}tos");
		$staticSitemap->addItem("{$domain}jobs");

		// write it
		$staticSitemap->write();

		// get URLs of sitemaps written
		$staticSitemapUrls = $staticSitemap->getSitemapUrls($domain);

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
	}
}
