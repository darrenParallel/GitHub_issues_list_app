<?php
require ($_SERVER["DOCUMENT_ROOT"] . "/control/api_class/api_class.php");
$api_connect = new api_class;

$list_issues = $api_connect->load_from_json();

//Page header
include ($_SERVER["DOCUMENT_ROOT"] . "/view/page_header.php");

include ($_SERVER["DOCUMENT_ROOT"] . "/view/add_issue.php");
include ($_SERVER["DOCUMENT_ROOT"] . "/view/list_issues.php");
include ($_SERVER["DOCUMENT_ROOT"] . "/view/error_list.php");

include ($_SERVER["DOCUMENT_ROOT"] ."/view/page_footer.php");
?>