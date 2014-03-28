<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['home/page/(:any)'] = "home/page";
$route['home/load/(:any)'] = "home/load";
$route['home/load_latest/(:any)'] = "home/load_latest";
$route['home/latest/(:any)'] = "home/latest";
$route['home/latest'] = "home/latest";
$route['home/(:any)'] = "home/index";


$route['f/(:any)/load_latest/(:any)'] = "f/load_latest";
$route['f/(:any)/latest/(:any)'] = "f/latest";
$route['f/(:any)/latest'] = "f/latest";
$route['f/(:any)/load/(:any)'] = "f/load";
$route['f/(:any)/add'] = "f/add";
$route['f/(:any)'] = "f/index";


/* User Routing*/
$route['user/(:any)/commentsLiked/(:any)'] = "user/commentsLiked";
$route['user/(:any)/commentsLiked'] = "user/commentsLiked";

$route['user/(:any)/comments/(:any)'] = "user/comments";
$route['user/(:any)/comments'] = "user/comments";

$route['user/(:any)/submitted/(:any)'] = "user/submitted";
$route['user/(:any)/submitted'] = "user/submitted";

$route['user/(:any)/liked/(:any)'] = "user/liked";
$route['user/(:any)/liked'] = "user/liked";

$route['user/getComments/(:any)/(:any)'] = "user/getComments";
$route['user/ban/(:any)'] = "user/ban";
$route['user/(:any)'] = "user/index";

$route['default_controller'] = "home";
$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */