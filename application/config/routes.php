<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['cart/.+'] = 'mall/cart';

// page 컨트롤러
$route['page/.+']  = 'page/index';
$route['admin/page/.+']  = 'page/index';
$route['vue/.+']  = 'page/index';

// match - board
$route['board/.+']  = 'board/index';
$route['notice/.+'] = 'board/index';

// match - page
$route['introduce/.+'] = 'page/index';
$route['partner/.+']   = 'page/index';
$route['whatis/.+']    = 'page/index';
$route['location/.+']  = 'page/index';

// SEO
$route['rss']     = 'seo/rss';
$route['sitemap'] = 'seo/sitemap';

// 관리자
// $route['admin/dashboard/.+'] 		= 'page/index';
$route['admin/etc/.+']            = 'page/index';
$route['admin/board/.+']          = 'board/index';
// 포인트
$route['admin/member/point']     = 'member/point';
$route['admin/member/point/(.+)'] = 'member/point/$1';

// 관리자 몰
$route['admin/order']                 = 'mall/order';
$route['admin/order/(.+)']            = 'mall/order/$1';
$route['admin/order_write/(.+)']      = 'mall/order_write/$1';
$route['admin/order_cancel/(.+)']     = 'mall/order_cancel/$1';
$route['admin/order_delete/(.+)']     = 'mall/order_delete/$1';
$route['admin/order_update_act']      = 'mall/order_update_act';

$route['admin/product']               = 'mall/product';
$route['admin/product/(:any)']        = 'mall/product/$1';
$route['admin/product/(:any)/(:any)'] = 'mall/product/$1/$2';
$route['admin/option_list/(:any)']           = 'mall/option_list/(:any)';
$route['admin/option_update']         = 'mall/option_update';
$route['admin/option_delete']         = 'mall/option_delete';


// $route['admin/order/()']  = 'mall/$1/$2';


// board method
// $route['.+/view/:any']           = 'board/index';
// $route['.+/write/:any']          = 'board/index';
// $route['.+/reply/:any']          = 'board/index';
// $route['.+/update/:any']         = 'board/index';
// $route['.+/write_act/:any']      = 'board/index';
// $route['.+/update_act/:any']     = 'board/index';
// $route['.+/delete/:any']         = 'board/index';
// $route['.+/check_password/:any'] = 'board/index';
// $route['.+/comment_insert/:any'] = 'board/index';
// $route['.+/comment_list/:any']   = 'board/index';
// $route['.+/comment_delete/:any'] = 'board/index';
// $route['.+/comment_update/:any'] = 'board/index';
