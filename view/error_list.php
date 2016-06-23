<?php
$error_log = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/error_log.txt");
$error_array = explode("{main}", $error_log);

$keys = array_keys($error_array);
$error_array = array_reverse($error_array);
$error_array = array_combine($keys, $error_array);
array_shift($error_array);
echo '<div id="err-div">';
echo '<h3>Most recent errors</h3>';
echo '<table class="tablegen" width="1100" border="1">';
echo '<tr><th width="50"><b>Break on</b></th><th width="80"><b>Date</b></th><th><b>Message Received</b></th></tr>';

//List most recent 5 errors only
foreach($error_array as $k => $ls) {
    $err_sets = explode(" - ", $ls);
    echo '<tr><td width="50">' . $err_sets[0] .  '</td><td width="80">' . $err_sets[1] . '</td><td>' . $err_sets[2] . '</td></tr>';
    if ($k === 5) break;
}
echo '</table>';
echo '<span>These are the most recent error logs, for more, view the error_log.txt</span></div>';
?>