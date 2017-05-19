<?php
# Steven Sullivan Ltd
error_reporting(NULL);
$TAB = 'TOOLS';

// Main include
include($_SERVER['DOCUMENT_ROOT']."/inc/main.php");

// Check user
if ($_SESSION['user'] != 'admin') {
    header("Location: /list/user");
    exit;
}

include($_SERVER['DOCUMENT_ROOT'].'/templates/header.html');
top_panel($user,$TAB);
?>
<div class="l-center units"><div class="l-unit"></div>

<h1>Tools</h1>
<p>A list of useful tools for VestaCP Administrators.</p>


<ul class="tab">
  <li><a href="#Users" class="tablinks" id="FirstTab" onclick="doTab(event, 'Users')">Users</a></li>
  <li><a href="#Domains" class="tablinks" onclick="doTab(event, 'Domains')">Domains</a></li>
  <li><a href="#Databases" class="tablinks" onclick="doTab(event, 'Databases')">Databases</a></li>
  <li><a href="#DNSDomains" class="tablinks" onclick="doTab(event, 'DNSDomains')">DNS Domains</a></li>
  <li><a href="#MailDomains" class="tablinks" onclick="doTab(event, 'MailDomains')">Mail Domains</a></li>
  <li><a href="#CronJobs" class="tablinks" onclick="doTab(event, 'CronJobs')">Cron Jobs</a></li>
  <li><a href="#LEUsers" class="tablinks" onclick="doTab(event, 'LEUsers')">Let's Encrypt Users</a></li>
</ul>

<?php
$UserData = shell_exec(VESTA_CMD.'v-list-users json');
$UserData = json_decode($UserData, true);
ksort($UserData);
?>

<div id="Users" class="tabcontent">
<?php
echo '<table class="sortable responstable">
<thead>
  <tr>
    <th width="100">Username</th>
    <th class="sorttable_nosort">First/Last Name</th>
    <th>Email</th>
    <th>Package</th>
    <th>Disk</th>
    <th>Bandwidth</th>
    <th width="50">Suspended</th>
    <th>Created</th>
  </tr>
</thead>
<tbody>';

foreach($UserData as $Username => $Array)
{
    $DiskPercent = (($Array['U_DISK'] / $Array['DISK_QUOTA']) * 100);
	$DiskPercent = ($DiskPercent>100) ? 100 : $DiskPercent;
    $BandPercent = (($Array['U_BANDWIDTH'] / $Array['BANDWIDTH']) * 100);
	$BandPercent = ($DiskPercent>100) ? 100 : $BandPercent;
    echo '<tr>
    <td align="left"><a href="/login/?loginas='. $Username .'" title="Login to this account">'. $Username .' <i class="fa fa-sign-in" style="color:green;"></i></a></td>
    <td>'. $Array['FNAME'] . ' ' . $Array['LNAME'] . '</td>
    <td>'. $Array['CONTACT'] .'</td>
    <td>'. $Array['PACKAGE'] .'</td>
    <td sorttable_customkey="'. $DiskPercent .'"><div class="progress"><div class="prgbar" style="width:'. $DiskPercent .'%;"></div></div></td>
    <td sorttable_customkey="'. $BandPercent .'"><div class="progress"><div class="prgbar" style="width:'. $BandPercent .'%;"></div></div></td>
    <td>'. $Array['SUSPENDED'] .'</td>
    <td>'. $Array['DATE'] .'</td>
    </tr>';
}

echo '</tbody>
</table>';
?>
</div>

<div id="Domains" class="tabcontent">
<?php
    //$Users = shell_exec(VESTA_CMD.'v-list-users json');
    //$Users = json_decode($Users, true);
    $Array = $Data = $tmpData = $AData = array();
    foreach($UserData as $Username=>$Array)
    {
        $Data = shell_exec(VESTA_CMD.'v-list-web-domains '. $Username .' json');
        $tmpData[$Username] = json_decode($Data, true);
        $tmpData[$Username]['tmp']['DISK_QUOTA'] = $Array['DISK_QUOTA'];
        $tmpData[$Username]['tmp']['BANDWIDTH'] = $Array['BANDWIDTH'];
    }
    $Data = $tmpData;
    ksort($Data);

echo '<table class="sortable responstable">
<thead>
  <tr>
    <th width="100">Username</th>
    <th>Domain</th>
    <th width="100">IP Address</th>
    <th width="30">SSL</th>
    <th>Disk</th>
    <th>Bandwidth</th>
    <th width="50">Suspended</th>
    <th>Created</th>
  </tr>
</thead>
<tbody>';

foreach($Data as $Username => $AData)
{
    foreach($AData as $Website => $Array)
    {
        if($Website=='tmp') continue;

        $DiskPercent = (($Array['U_DISK'] / $Data[$Username]['tmp']['DISK_QUOTA']) * 100);
		$DiskPercent = ($DiskPercent>100) ? 100 : $DiskPercent;
        $BandPercent = (($Array['U_BANDWIDTH'] / $Data[$Username]['tmp']['BANDWIDTH']) * 100);
		$BandPercent = ($BandPercent>100) ? 100 : $BandPercent;
        echo '<tr>
        <td align="left"><a href="/login/?loginas='. $Username .'" title="Login to this account">'. $Username .' <i class="fa fa-sign-in" style="color:green;"></i></a></td>
        <td><a href="http://'.$Website.'" title="Visit this website" target="_blank">'. $Website . ' <i class="fa fa-eye" style="color:green;"></i></a></td>
        <td>'. $Array['IP'] .'</td>
        <td>'. $Array['SSL'] .'</td>
        <td sorttable_customkey="'. $DiskPercent .'"><div class="progress"><div class="prgbar" style="width:'. $DiskPercent .'%;"></div></div></td>
        <td sorttable_customkey="'. $BandPercent .'"><div class="progress"><div class="prgbar" style="width:'. $BandPercent .'%;"></div></div></td>
        <td>'. $Array['SUSPENDED'] .'</td>
        <td>'. $Array['DATE'] .'</td>
        </tr>';
    }
}

echo '</tbody>
</table>';
?>
</div>

<div id="Databases" class="tabcontent">
<?php

    //$Users = shell_exec(VESTA_CMD.'v-list-users json');
    //$Users = json_decode($Users, true);
    $Array = $Data = $tmpData = $AData = array();
    foreach($UserData as $Username=>$Array)
    {
        $Data = shell_exec(VESTA_CMD.'v-list-databases '. $Username .' json');
        $tmpData[$Username] = json_decode($Data, true);
    }
    $Data = $tmpData;
    ksort($Data);

echo '<table class="sortable responstable">
<thead>
  <tr>
    <th width="100">Username</th>
    <th>DB Name</th>
    <th>DB User</th>
    <th width="50">Host</th>
    <th width="50">Disk Usage</th>
    <th width="50">Suspended</th>
    <th width="70">Created</th>
  </tr>
</thead>
<tbody>';

foreach($Data as $Username => $AData)
{
    foreach($AData as $Cron => $Array)
    {
        echo '<tr>
        <td align="left"><a href="/login/?loginas='. $Username .'" title="Login to this account">'. $Username .' <i class="fa fa-sign-in" style="color:green;"></i></a></td>
        <td>'. $Array['DATABASE'] . '</td>
        <td>'. $Array['DBUSER'] .'</td>
        <td>'. $Array['HOST'] .'</td>
        <td sorttable_customkey="'. $Array['U_DISK'] .'">'. $Array['U_DISK'] .'MB</td>
        <td>'. $Array['SUSPENDED'] .'</td>
        <td>'. $Array['DATE'] .'</td>
        </tr>';
    }
}

echo '</tbody>
</table>';
?>
</div>

<div id="DNSDomains" class="tabcontent">
<?php
    $Array = $Data = $tmpData = $AData = array();
    foreach($UserData as $Username=>$Array)
    {
        $Data = shell_exec(VESTA_CMD.'v-list-dns-domains '. $Username .' json');
        $tmpData[$Username] = json_decode($Data, true);
    }
    $Data = $tmpData;
    ksort($Data);

echo '<table class="sortable responstable">
<thead>
  <tr>
    <th width="100">Username</th>
    <th>Domain</th>
    <th width="100">IP Address</th>
    <th width="50">Total Records</th>
    <th width="50">Suspended</th>
    <th width="70">Created</th>
  </tr>
</thead>
<tbody>';

foreach($Data as $Username => $AData)
{
    foreach($AData as $Domain => $Array)
    {
        echo '<tr>
        <td align="left"><a href="/login/?loginas='. $Username .'" title="Login to this account">'. $Username .' <i class="fa fa-sign-in" style="color:green;"></i></a></td>
        <td>'. $Domain . '</td>
        <td>'. $Array['IP'] .'</td>
        <td>'. $Array['RECORDS'] .'</td>
        <td>'. $Array['SUSPENDED'] .'</td>
        <td>'. $Array['DATE'] .'</td>
        </tr>';
    }
}

echo '</tbody>
</table>';
?>
</div>

<div id="MailDomains" class="tabcontent">
<?php
    $Array = $Data = $tmpData = $AData = array();
    foreach($UserData as $Username=>$Array)
    {
        $Data = shell_exec(VESTA_CMD.'v-list-mail-domains '. $Username .' json');
        $tmpData[$Username] = json_decode($Data, true);
    }
    $Data = $tmpData;
    ksort($Data);

echo '<table class="sortable responstable">
<thead>
  <tr>
    <th width="100">Username</th>
    <th>Domain</th>
    <th width="50">AntiSpam</th>
    <th width="50">AntiVirus</th>
    <th width="20">DKIM</th>
    <th width="20">Accounts</th>
    <th width="20">Catchall</th>
    <th width="20">Disk Used</th>
    <th width="50">Suspended</th>
    <th width="70">Created</th>
  </tr>
</thead>
<tbody>';

foreach($Data as $Username => $AData)
{
    foreach($AData as $Domain => $Array)
    {
        echo '<tr>
        <td align="left"><a href="/login/?loginas='. $Username .'" title="Login to this account">'. $Username .' <i class="fa fa-sign-in" style="color:green;"></i></a></td>
        <td>'. $Domain . '</td>
        <td>'. $Array['ANTISPAM'] .'</td>
        <td>'. $Array['ANTIVIRUS'] .'</td>
        <td>'. $Array['DKIM'] .'</td>
        <td>'. $Array['ACCOUNTS'] .'</td>
        <td>'. $Array['CATCHALL'] .'</td>
        <td sorttable_customkey="'. $Array['U_DISKL'] .'">'. $Array['U_DISKL'] .'MB</td>
        <td>'. $Array['SUSPENDED'] .'</td>
        <td>'. $Array['DATE'] .'</td>
        </tr>';
    }
}

echo '</tbody>
</table>';
?>
</div>

<div id="CronJobs" class="tabcontent">
<?php
    //$Users = shell_exec(VESTA_CMD.'v-list-users json');
    //$Users = json_decode($Users, true);
    $Array = $Data = $tmpData = $AData = array();
    foreach($UserData as $Username=>$Array)
    {
        $Data = shell_exec(VESTA_CMD.'v-list-cron-jobs '. $Username .' json');
        $tmpData[$Username] = json_decode($Data, true);
    }
    $Data = $tmpData;
    ksort($Data);

echo '<table class="sortable responstable">
<thead>
  <tr>
    <th width="100">Username</th>
    <th>Command</th>
    <th class="sorttable_nosort" width="20">Min</th>
    <th class="sorttable_nosort" width="20">Hour</th>
    <th class="sorttable_nosort" width="20">Month</th>
    <th class="sorttable_nosort" width="20">Day</th>
    <th width="50">Suspended</th>
    <th width="70">Created</th>
  </tr>
</thead>
<tbody>';

foreach($Data as $Username => $AData)
{
    foreach($AData as $Cron => $Array)
    {
        echo '<tr>
        <td align="left"><a href="/login/?loginas='. $Username .'" title="Login to this account">'. $Username .' <i class="fa fa-sign-in" style="color:green;"></i></a></td>
        <td>'. $Array['CMD'] . '</td>
        <td>'. $Array['MIN'] .'</td>
        <td>'. $Array['HOUR'] .'</td>
        <td>'. $Array['MONTH'] .'</td>
        <td>'. $Array['WDAY'] .'</td>
        <td>'. $Array['SUSPENDED'] .'</td>
        <td>'. $Array['DATE'] .'</td>
        </tr>';
    }
}

echo '</tbody>
</table>';
?>
</div>

<div id="LEUsers" class="tabcontent">
<?php
    //$Users = shell_exec(VESTA_CMD.'v-list-users json');
    //$Users = json_decode($Users, true);
    $Array = $Data = $tmpData = $AData = array();
    foreach($UserData as $Username=>$Array)
    {
        $Data = shell_exec(VESTA_CMD.'v-list-letsencrypt-user '. $Username .' json');
        if(strstr($Data, 'Error')==TRUE)
          continue;
        $tmpData[$Username] = json_decode(str_replace('"THUMB', '"THUMB"', $Data), true)[$Username];
    }
    //print_r($tmpData);
    $Data = $tmpData;
    ksort($Data);

echo '<table class="sortable responstable">
<thead>
  <tr>
    <th width="100">Username</th>
    <th>Assigned Email</th>
  </tr>
</thead>
<tbody>';

foreach($Data as $Username => $Array)
{
    //foreach($AData as $Cron => $Array)
    //{
        echo '<tr>
        <td align="left"><a href="/login/?loginas='. $Username .'" title="Login to this account">'. $Username .' <i class="fa fa-sign-in" style="color:green;"></i></a></td>
        <td>'. $Array['EMAIL'] . '</td>
        </tr>';
    //}
}

echo '</tbody>
</table>';
?>
</div>


</div>
<script src="https://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
<script>
function doTab(evt, Tab) {
    // Declare all variables
    var i, tabcontent, tablinks;

    evt.preventDefault();

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the link that opened the tab
    document.getElementById(Tab).style.display = "block";
    evt.currentTarget.className += " active";
}
//$('ul.tab a:first-child').click();
</script>
<style>
<!--
/* Sortable tables */
table.sortable thead {
    background-color:#eee;
    color:#666666;
    font-weight: bold;
    cursor: default;
}
table.sortable th:not(.sorttable_sorted):not(.sorttable_sorted_reverse):not(.sorttable_nosort):after { 
    content: " \25B4\25BE" 
}

.progress {
        border: 1px solid #5d5d5d;
        height: 10px;
        width: 100px;
}
.progress .prgbar {
        background: #ff8e61;
        position: relative;
        height: 10px;
        z-index: 999;
}


/* Style the list */
ul.tab {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
}

/* Float the list items side by side */
ul.tab li {float: left;}

/* Style the links inside the list items */
ul.tab li a {
    display: inline-block;
    color: black;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
    transition: 0.3s;
    font-size: 17px;
}

/* Change background color of links on hover */
ul.tab li a:hover {background-color: #ddd;}

/* Create an active/current tablink class */
ul.tab li a:focus, ul.tab li a.active {background-color: #ccc;}

/* Style the tab content */
.tabcontent {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
}

.tabcontent {
    -webkit-animation: fadeEffect 1s;
    animation: fadeEffect 1s; /* Fading effect takes 1 second */
}

@-webkit-keyframes fadeEffect {
    from {opacity: 0;}
    to {opacity: 1;}
}

@keyframes fadeEffect {
    from {opacity: 0;}
    to {opacity: 1;}
}

.responstable {
  margin: 1em 0;
  width: 100%;
  overflow: hidden;
  background: #FFF;
  color: #5d5d5d;
  border-radius: 10px;
  border: 1px solid #167F92;
  font-size:12px;
}
.responstable tr {
  border: 1px solid #D9E4E6;
}
.responstable tr:nth-child(odd) {
  background-color: #efefef;
}
.responstable th {
  display: none;
  border: 1px solid #FFF;
  background-color: #5d5d5d;
  color: #FFF;
  padding: 1em;
}
.responstable th:first-child {
  display: table-cell;
}
.responstable th:nth-child(2) {
  display: table-cell;
}
.responstable th:nth-child(2) span {
  display: none;
}
.responstable th:nth-child(2):after {
  content: attr(data-th);
}
@media (min-width: 480px) {
  .responstable th:nth-child(2) span {
    display: block;
  }
  .responstable th:nth-child(2):after {
    display: none;
  }
}
.responstable td {
  display: block;
  word-wrap: break-word;
  max-width: 7em;
}
.responstable td:first-child {
  display: table-cell;
  border-right: 1px solid #D9E4E6;
}
@media (min-width: 480px) {
  .responstable td {
    border: 1px solid #D9E4E6;
  }
}
.responstable th, .responstable td {
  text-align: left;
  margin: .5em 1em;
}
@media (min-width: 480px) {
  .responstable th, .responstable td {
    display: table-cell;
    padding: 1em;
  }
}
//-->
</style>

<div style="border:1px solid grey; padding:20px;text-align:center;">Made by <a href="https://blog.ss88.uk/vestacp-tools-plugin" target="_blank">Steven Sullivan</a> - Version 1.1</div>

<script>
window.onload = function(e){ document.getElementById("FirstTab").click() }
</script>

<?php

// Footer
include($_SERVER['DOCUMENT_ROOT'].'/templates/footer.html');

?>
