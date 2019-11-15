<?php
$full_uri = $_SERVER['REQUEST_URI'];
$uri = preg_split('/\?/',$full_uri);
$route_arr = preg_split('/\//',$uri[0]);

switch ($route_arr[1]){
    case 'categories':include './routes/route_categories.php';
        break;
    case 'subcategories':include './routes/route_subcategories.php';
        break;
    case 'languages':include './routes/route_language.php';
        break;
    case 'colors':include './routes/route_colors.php';
        break;
    case 'locale':include './routes/route_locale.php';
        break;
    case 'admin':include './routes/route_admin.php';
        break;
    default:echo '404';
}