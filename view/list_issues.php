<?php ?>
 <div id="list-div">
     <table id="datatable">
      <thead>
          <tr> 
            <th width="100"><b>Client Name</b></th>
            <th width="90"><b>Action item/Request</b></th>
            <th width="110"><b>Description</b></th>
            <th width="80"><b>GH number</b></th>
            <th width="100"><b>Priority</b></th>
            <th width="180"><b>Category</b></th>
            <th width="110"><b>Assigned To</b></th>
            <th width="100"><b>Comments</b></th>
            <th width="40"><b>Status</b></th>
          </tr>
      </thead>
      <?php 
      	if (count(array_filter($list_issues)) != 0) {
      	    echo "<tbody>";
            $issueOutput = (isset($list_issues["list"])) ? $list_issues["list"] : $list_issues;
      		foreach ($issueOutput as $issue) {
      ?>
      
                  <tr >
                  	<td><?=$issue["clientName"]?></td>
                  	<td><?=$issue["action"]?></td>
                  	<td><?=$issue["description"]?></td>
                  	<td><?=$issue["GHNumber"]?></td>
                  	<td><?=$issue["priority"]?></td>
                  	<td><?=$issue["category"]?></td>
                  	<td><?=$issue["assignee"]?></td>
                  	<td><?=$issue["comments"]?></td>
                  	<td><?=$issue["state"]?></td>
                  </tr>
 
      <?php
    		}
    	echo "</tbody>";
    	}
    	
      ?>
      	<tr>
      		<td colspan="9" align="right" style="background-color: #3782ff">
            <a id="refresh_json" href="#">Refresh All</a>
            </td>
      	</tr>
      </table>
  </div>