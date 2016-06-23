<?php 

//Retrieve the labels from the main class
$liveLabels = $api_connect->list_labels();
$clientArray = $liveLabels["cl"];
$priorityArray = $liveLabels["pr"];
$categoryArray = $liveLabels["cat"];

?>
<div id="add-div">
    <h3>Add an Issue</h3>
        <table class="tablegen" width="960" border="0">
          <tr> 
            <th width="80"><b>Client Name</b></th>
            <th width="80"><b>Action item/Request</b></th>
            <th width="120"><b>Description</b></th>
            <th width="80"><b>Priority</b></th>
            <th width="100"><b>Category</b></th>
            <th width="120"></th>
          </tr>
          <tr>
           <td>
              <select name="clientName" id="clientName">
              <option value="" selected="selected">Please select</option>
              <?php 
              foreach ($clientArray as $clName) {
                  echo "<option value=\"{$clName}\">{$clName}</option>";
              };
              ?>
              </select>
            </td>
            <td>
              <input name="actionRequest" type="text" id="actionRequest" size="18" maxlength="100" required /></td>
            <td>
              <textarea name="issueDescription" id="issueDescription" cols="25" rows="1" required /></textarea></td>
            <td>
              <select name="issuePriority" id="issuePriority">
              <?php 
              foreach ($priorityArray as $prName) {
                  $prSelect = ($prName === "Low Priority") ? "selected=\"selected\"" : "";
                  echo "<option value=\"{$prName}\" {$prSelect}>{$prName}</option>";
              };
              ?>
              </select>
            </td>
            <td>
              <select name="issueCategory" id="issueCategory">
              <?php 
              foreach ($categoryArray as $catName) {
                  $catSelect = ($catName === "bug") ? "selected=\"selected\"" : "";
                  echo "<option value=\"{$catName}\" {$catSelect}>{$catName}</option>";
              };
              ?>
              </select>
            </td>
            <td align="center"><a id="add_issue" href="#"><img src="images/submit.png" alt="submit" /></a></td>
          </tr>
        </table>
    <h5></h5>
</div>