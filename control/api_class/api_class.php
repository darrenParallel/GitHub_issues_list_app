<?php

class api_class {
   
    function __construct() {
        require_once ($_SERVER["DOCUMENT_ROOT"] . "/control/settings.php");
        require_once ($_SERVER["DOCUMENT_ROOT"] . "/lib/github_client/vendor/autoload.php");
        
        $this->client = new \Github\Client();
        $this->client->authenticate($_SESSION["oauth_token"], NULL, Github\Client::AUTH_HTTP_TOKEN);   
    }
    
    public function request_list($page = 1, $pageIssues = 10, $state = "open", $allIssues = 0) {
        try {
            $issuesApi = $this->client->api("issues");
            $paginator = new \Github\ResultPager($this->client);
            
            if ($allIssues === 1) {
                $issuesSet = $paginator->fetchAll($issuesApi, "all", [OWNER, REPO]);
            } else {
                $issuesSet = $paginator->fetch($issuesApi, "all", [OWNER, REPO, array("state" => "open", "page" => $page, "per_page" => $pageIssues)]);
            };
            
            foreach ($issuesSet as $issue) {
                unset($clientArray);
                unset($category);
                
                //Fetch GH Number
                $numberHld = $issue["number"];
                $number = (!empty($numberHld)) ? $numberHld : null;
                
                //Fetch Comments Count
                $commentTotal = $issue["comments"];
                
                if ($commentTotal != 0) {
                    unset($commentsList);
                    $commentsList = $this->client->api("issues")->comments()->all(OWNER, REPO, $number);
                    
                    $commentArray = array();
                    foreach ($commentsList as $val) {
                        $commentArray[] = $val["body"];
                    }
                    
                    $commentString = implode("\n", array_filter($commentArray));
                }
                
               // Retrieve Labels (Priority, Clients, Categories)
                if (!empty($issue["labels"])) {
                    
                    foreach ($issue["labels"] as $val) 
                    {
                        $label = $val["name"];
                        $findField = strtoupper(substr($label, 0, 2));
                        switch ($findField) {
                            case "C:":
                                $clientArray[] = trim(substr($label, 2));
                                break;
                            case "P:":
                                $priority = strtoupper(trim(substr($label, 2)));
                                break;
                            default:
                                $category[] = ucfirst(strtolower($label));
                        }   
                    }
                    
                    $clientName = (!empty($clientArray)) ? implode("\n", array_unique($clientArray)) : "";
                    $categoryString = (!empty($category)) ? implode("\n", array_unique($category)) : "";        
                }
                
                //Incude standard fields
                $issueTitle = $issue["title"];
                $issueDescription = $issue["body"];
                $issueAssignee = $issue["assignee"]["login"];
                $issueState = $issue["state"];
                                
                //assign array group
                $list[] = array(
                    "clientName" => (isset($clientName)) ? $clientName : "",
                    "action" => (isset($issueTitle)) ? $issueTitle : "",
                    "description" => (isset($issueDescription)) ? $issueDescription : "",
                    "GHNumber" => (isset($number)) ? $number : "",
                    "priority" => (isset($priority)) ? $priority : "",
                    "category" => (isset($categoryString)) ? $categoryString : "",
                    "assignee" => (isset($issueAssignee)) ? $issueAssignee : "",
                    "comments" => (isset($commentString)) ? $commentString : "",
                    "state" => (isset($issueState)) ? $issueState : ""
                ); 
            }

        } catch (exception $e) {
            $errorLog = $e;
            $errorStatus = "Issue List - " . date("d-m-Y h:i:s A") . " - " . $errorLog . "\n\n";
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/error_log.txt", $errorStatus, FILE_APPEND | LOCK_EX); 
            $return["error_log"] = '<font size="3" color="red"><b>Load from LIVE Failed: Error Logged, Please view error_log.txt</b></font>';
        }
        
        $this->removeTagging($list);
                        
        $return = array(
            "list" => (isset($list)) ? $list : array(),
            "nextPage" => ($paginator->hasNext()) ? 1 : 0,
            "prevPage" => ($paginator->hasPrevious()) ? 1 : 0
        );
        
        return $return;
    }

     /**
     * Generates a list of live labels and groups the nessessary ones appropriately.
     * @return Array
     */
     public function list_labels() {
         $callArray = array();
         $labelSearch = $this->client->api("issue")->labels()->all(OWNER, REPO);
         
         foreach ($labelSearch as $val) {
             $label = $val["name"];
             $ct = strpos($label, ":");
             $findField = strtoupper(substr($label, 0, $ct+1));
             switch ($findField) {
                 case "C:":
                     $callArray["cl"][] = trim(substr($label, 2));
                     break;
                 case "P:":
                     $callArray["pr"][] = trim(substr($label, 2));
                     break;
                 case "CAT:":
                     $callArray["cat"][] = trim(substr($label, 4));
                     break;
              }   
          }

          return $callArray;
     }
    
     /**
     * Adds New Issue to Github issue list
     * 
     * @param  converted $_POST data from Router 
     * @return Success Message or Error Message: String
     */
    public function add_issue() {
        try {
            
            $labels = array(
                (!empty($this->addClientName)) ? "C: " . $this->addClientName : "",
                (!empty($this->addIssuePriority)) ? "P: " . $this->addIssuePriority : "",
                (!empty($this->addIssueCategory)) ? "Cat: " . $this->addIssueCategory : ""
            );

            $title = (!empty($this->addActionRequest)) ? $this->addActionRequest : "";
            $body = (!empty($this->addIssueDescription)) ? $this->addIssueDescription : "";

            $this->client->api("issues")->create(OWNER, REPO, array("title" => $title, "body" => $body, "labels" => $labels));
            
            $this->append_json();
            
        } catch (exception $e) {
            $errorLog = $e;
            $errorStatus = "Add Issue - " . date("d-m-Y h:i:s A") . " - " . $errorLog . "\n\n";
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/error_log.txt", $errorStatus, FILE_APPEND | LOCK_EX);
        }
        
        return;        
    }

     /**
     * Reloads from GITHUB and saves to JSON file.
     * 
     * @param  None
     * @return Array or included error log
     */
    public function add_to_json($request=false) {

        try {
                       
           if (!is_dir($_SERVER["DOCUMENT_ROOT"] . "/tmp/")) {
                mkdir($_SERVER["DOCUMENT_ROOT"] . "/tmp/");
           }
           if ($request != false) {
              $data = $request;
           } else {
              $data = $this->request_list(Null,Null,Null,1);  
           }
           $jsonData = json_encode($data, JSON_PRETTY_PRINT);

          if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/tmp/issues.json"))
               unlink($_SERVER["DOCUMENT_ROOT"] . "/tmp/issues.json");
               // End if;
               
               file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/tmp/issues.json", $jsonData, FILE_APPEND | LOCK_EX);
               unset($jsonData);
        
        } catch (exception $e) {
            $errorLog = $e;
            $errorStatus = "Add to JSON - " . date("d-m-Y h:i:s A") . " - " . $errorLog . "\n\n";
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/error_log.txt", $errorStatus, FILE_APPEND | LOCK_EX);
            $jsonData["error_log"] = '<font size="3" color="red"><b>Add to JSON Failed: Error Logged, Please view error_log.txt</b></font>';
        }
        
        $jsonData = $this->load_from_json();
        
        return $jsonData;
    }


     /**
     * Retrieves saved JSON data.
     * 
     * @param  bool $setArray output is true. // update planned to use router and ajax
     * @return (array or JSON) or included error log
     */
    public function load_from_json($setArray = true) {
        
        try {
            $retrieveJson = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/tmp/issues.json");
            if ($setArray) {
                $returnJson = json_decode($retrieveJson, true);
            } else {
                $returnJson = $retrieveJson;
            };
        } catch (exception $e) {
            $errorLog = $e;
            $errorStatus = "Load From JSON - " . date("d-m-Y h:i:s A") . " - " . $errorLog . "\r\n\r\n";
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/error_log.txt", $errorStatus, FILE_APPEND | LOCK_EX);
        }

        return $returnJson;
    }
    
    
     /**
     * This simply adds to the JSON File to avoid a full reload.  
     * This is called by the add_issue function to update the JSON Data
     * 
     */
     public function append_json ($page = 1, $per_page = 1) {

         $currentList = $this->load_from_json();
         $OutputAr = $this->request_list($page, $per_page); //page, total per page
         
         $updateArray = array();
         $updateArray["list"] = array_merge($OutputAr["list"], $currentList["list"]);

         $jsonData = $this->add_to_json($updateArray);
         
         return $jsonData;
         }    
    
     /**
     * Removes html & other character tags.
     * 
     * @param  array $input array is modified by reference.
     * @return bool
     */
    public function removeTagging(&$input) {
        if (is_array($input)) {
            // do a recursive filter throughout the array
            array_walk_recursive($input, "self::removeTagging", $input);
        } else if (is_string($input)) {
            $input = strip_tags($input);
        }
        
        return true;
    }
 }