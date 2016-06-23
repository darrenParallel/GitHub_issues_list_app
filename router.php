<?php
	require_once($_SERVER["DOCUMENT_ROOT"] . "/control/api_class/api_class.php");
	$issueCall = new api_class;
	
    $set = $_GET["set"];
    switch ($set) {
        case "1":
            $issueCall->addClientName = (isset($_GET["clientName"])) ? $_GET["clientName"] : "";
            $issueCall->addIssuePriority = (isset($_GET["issuePriority"])) ? $_GET["issuePriority"] : "";
            $issueCall->addIssueCategory = (isset($_GET["issueCategory"])) ? $_GET["issueCategory"] : "";
            $issueCall->addActionRequest = (isset($_GET["actionRequest"])) ? $_GET["actionRequest"] : "";
            $issueCall->addIssueDescription = (isset($_GET["issueDescription"])) ? $_GET["issueDescription"] : "";
            $issueCall->add_issue();
        break;
        case "3":
            $issueCall->add_to_json();
        break;
    }

?>