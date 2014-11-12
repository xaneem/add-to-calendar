<?php require_once "clusterdev.addToCalendar.php"; 

//Set up the event data.
$event = array(
            'summary' => 'Pure HTML addToCalendar buttons',
            'description' => 'No fancy javscript - it works even inside emails. Available for Google, Outlook, Yahoo, iCal and Live.',
            'location' => 'http://www.clusterdev.com/addToCalendar',
            'start' => '2015-01-01T10:00:00',
            'end' => '2015-01-01T12:00:00',
            // 'duration' => '20', //You can enter either end date or duration. If none are provided, it is defaulted to 30 minutes
            'organizer' => 'ClusterDev',
            'organizer_email' => 'info@clusterdev.com',
         );

try{
	//Create a new object with the data.
    $event_buttons = new \clusterdev\addToCalendar($event);

    $buttons_html = $event_buttons->getButtons(
    	array('google', 'outlook', 'yahoo','ical', 'live', 'other'), //Array of strings for calendar provider
    	FALSE, //Change this to TRUE to use inline styles.
    	'http://www.clusterdev.com/add-to-calendar/ics.php' //The full web URL to the ics generator. Remove this line to use inline ics generator (does not work inside emails)
    );

    //To get event link for a specific provider, use the 
    // $google_link = $event_buttons->getLink('google');
    // $ical_link =  $event_buttons->getLink('ical', 'http://www.clusterdev.com/add-to-calendar/ics.php');
} catch(Exception $e){
  //$error_msg = $e->getMessage(); 
}

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>ClusterDev addToCalendar Demo</title>
    <style>
    	<?php 
    	//Use this only if you set inline_style=FALSE
    	echo \clusterdev\addToCalendar::printStyle(); ?>
    </style>
</head>
<body>
<h1>addToCalendar</h1>

<p>
<?php echo $buttons_html; ?>
</p>

<p>These buttons work even inside emails. Take a look at clusterdev.addToCalendar.php to understand how it works (it's documented).
<br><br>
<a href="https://github.com/xaneem/add-to-calendar" target="_blank">GitHub</a> | <a href="http://www.clusterdev.com/add-to-calendar/" target="_blank">ClusterDev</a>
</p>

</body>
</html>
