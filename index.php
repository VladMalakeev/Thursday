<?php
header("Content-Type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
//error_reporting(E_ERROR | E_PARSE);
include './functions/functions.php';
include './functions/Responses.php';

require './database/DataBase.php';
require './database/DBTables.php';
require './database/DBLanguage.php';
require './database/DBStrings.php';
require './database/DBCategories.php';
require './database/DBSubcategories.php';
require './database/DBColors.php';
require './database/DBLocale.php';

require './routes/route_index.php';




