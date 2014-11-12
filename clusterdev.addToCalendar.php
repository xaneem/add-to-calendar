<?php

namespace clusterdev{
	class addToCalendar{

		private $summary, $start, $duration, $end, $location, $description, $organizer, $organizer_email;

		/**
		 * Constructor
		 * Sets NULL values for the private variables. Calls the eventData function
		 * if an array is passed during creation of object
		 *
		 * @param array $data_array (Optional) key-value pair with event details
		 * @return void
		 **/
		function __construct($data_array = NULL) {
			//Sets default values as null
			$this->summary = $this->start = $this->duration = $this->end = $this->location = $this->description = $this->organizer = $this->organizer_email = NULL;
			
			//If data is provided, use this as event data.
			if(is_array($data_array))
				$this->eventData($data_array);
		}

		/**
		 * Updates the event data stored in private variables.
		 * The data is to be provided as a key-value pair.
		 * To generate buttons for multiple events, 
		 * run this function again after the button is generated with the new input values.
		 *
		 * We escape any html tags using htmlentities.
		 * @param array $data_array key-value pair with event details
		 * @return void
		 **/
		public function eventData($data_array){
			$this->summary = array_key_exists('summary', $data_array)?$data_array['summary']:NULL;
			$this->summary = htmlentities($this->summary, ENT_QUOTES);

			$this->description = array_key_exists('description', $data_array)?$data_array['description']:NULL;
			$this->description = htmlentities($this->description, ENT_QUOTES);

			$this->start = array_key_exists('start', $data_array)?$data_array['start']:NULL;
			$this->start = htmlentities($this->start, ENT_QUOTES);

			$this->end = array_key_exists('end', $data_array)?$data_array['end']:NULL;
			$this->end = htmlentities($this->end, ENT_QUOTES);

			$this->duration = array_key_exists('duration', $data_array)?$data_array['duration']:NULL;
			$this->duration = htmlentities($this->duration, ENT_QUOTES);

			$this->location = array_key_exists('location', $data_array)?$data_array['location']:NULL;
			$this->location = htmlentities($this->location, ENT_QUOTES);

			$this->organizer = array_key_exists('organizer', $data_array)?$data_array['organizer']:NULL;
			$this->organizer = htmlentities($this->organizer, ENT_QUOTES);

			$this->organizer_email = array_key_exists('organizer_email', $data_array)?$data_array['organizer_email']:NULL;
			$this->organizer_email = htmlentities($this->organizer_email, ENT_QUOTES);
		}

		/**
		 * Returns the HTML for event buttons. The event data should have been added using eventData().
		 * If the required data is not available, FALSE is returned.
		 *
		 * @param array $buttons Names of calendar providers to create buttons for
		 * @param boolean $inline_style Turns inline styles on or off
		 * @param string $generate_url URL to the ics generator. 
		 *		  If left blank, ics is generated inline (Does not work for emails)
		 *
		 * @return string HTML code for the buttons. FALSE if incomplete data.
		 **/
		public function getButtons($buttons, $inline_style = FALSE, $generate_url = NULL){
			//The start, summary, and end/duration are required. If both duration and end is specified, end is used.
			if(!$this->start || !$this->summary || (!$this->end && !$this->duration))
				return FALSE;

			//List of available functions. Any other values are ignored.
			$available = array(
				'google' => 'Google',
				'yahoo' => 'Yahoo', 
				'ical' => 'iCal', 
				'outlook' => 'Outlook',
				'live' => 'Live',
				'other' => 'Other'
			);

			$buttons = array_map('strtolower', $buttons);

			//Top border colors for inline buttons (Does not work for CSS styled buttons)
			$colors = array("#059BF5", "#FF7837", "#14ABA7", "#A2A2A2", "#C4C400", "#FF77BB");
			
			$serial = 0;
			$code = '';

			foreach ($buttons as $key) {
				//Display only those buttons that are provided in $buttons
				if(array_key_exists($key, $available)){		
					if($inline_style){
						//If inline_style is TRUE, generate buttons with inline styles.
						$code .= '<a href="'.$this->$key($generate_url).'" target="_blank"><span style="display: inline-block; line-height: 110%; background: #F3F3F3 repeat-x; text-decoration: none; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: 300; color: #303030; cursor: pointer; padding: 7px 15px 8px 14px; border: 1px solid #E5E5E5; text-align: center; min-width: 50px; margin: 0px 2px 0px 0px; -moz-border-radius: 4px; -webkit-border-radius: 4px; border-top: 4px solid '.$colors[$serial].'">'.$available[$key].'</span></a>';
						$serial++;
					}else{
						//Otherwise, create buttons with add-event-btn and btn-{serial} class.
						$serial++;
						$code .= '<a href="'.$this->$key($generate_url).'" target="_blank"><span class="add-event-btn btn-'.$serial.'">'.$available[$key].'</span></a>';
					}
					//Add a newline for better HTML readability
					$code .= "\n";
				}
			}
			return $code;
		}

		/**
		 * Get the button URL for a particular calendar provider. 
		 * The event data should have been added using eventData().
		 *
		 * @param string $provider The calendar provider to generate URL for.
		 * @param string $generate_url URL to the ics generator. 
		 *		  If left blank, ics is generated inline (Does not work for emails)
		 *
		 * @return string The URL to add event to the provider. FALSE if incomplete data.
		 **/
		public function getLink($provider, $generate_url = NULL){

			//The start, summary, and end/duration are required. If both duration and end is specified, end is used.
			if(!$this->start || !$this->summary || (!$this->end && !$this->duration))
				return FALSE;

			$available = array(
				'google' => 'Google',
				'yahoo' => 'Yahoo', 
				'ical' => 'iCal', 
				'outlook' => 'Outlook',
				'live' => 'Live',
			);

			if(array_key_exists($provider, $available))
				return $this->$provider($generate_url);
			else
				return false;
		}

		/**
		 * Returns the button styles. These is used only if $inline_style is FALSE.
		 * The styles should be printed inside a <style> tag.
		 * This function can be called even before creating an object.
		 * For example:
		 * echo \clusterdev\addToCalendar::printStyle();
		 *
		 * If you would like to style the buttons yourself, feel free to do so.
		 *
		 * @return string The styles for add-event-btn and btn-1 to btn-6.
		 **/
		public static function printStyle(){
			return ".add-event-btn{ display: inline-block; position: relative; line-height: 110%; background: #F3F3F3 repeat-x; text-decoration: none;  font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: 300;  color: #303030; cursor: pointer; padding: 7px 15px 8px 14px; border: 1px solid #E5E5E5;  text-align: center; min-width: 50px;  margin: 0px 2px 0px 0px; -moz-border-radius: 4px; -webkit-border-radius: 4px; }  .add-event-btn:hover{ background: #EBEBEB; }  .btn-1{ border-top: 4px solid #059BF5; }  .btn-2{ border-top: 4px solid #FF7837; }  .btn-3{ border-top: 4px solid #14ABA7; }  .btn-4{ border-top: 4px solid #A2A2A2; }  .btn-5{ border-top: 4px solid #C4C400; } .btn-6{ border-top: 4px solid #FF77BB; }";
		}

		/**
		 * Formats the input time in ISO format.
		 * @param string $datetime The input datetime
		 * @return string Datetime in ISO format.
		 **/
		private function formatTime($datetime){
			$format = new \DateTime($datetime);
			return preg_replace("/-|:|\.\d+/", "", $format->format('Y-m-d\TH:i:s'));
		}

		/**
		 * Calculates the end time. Assumes that start and duration is set.
		 * If end time is set, returns the end time in ISO format.
		 *
		 * @return string 
		 **/
		private function calculateEndTime(){
			if($this->end !== NULL && $this->end !== '')
				return $this->formatTime($this->end);

			if(!is_numeric($this->duration))
				$this->duration = 30;

			$timeObj = new \DateTime($this->start);
			date_add($timeObj, date_interval_create_from_date_string($this->duration.' minutes'));
			return preg_replace("/-|:|\.\d+/", "", $timeObj->format('Y-m-d\TH:i:s'));
		}

		/**
		 * Returns the duration of the event
		 * If no values are specified for end time and duration, 30 minutes is assumed.
		 *
		 * @return integer Duration of the event in minutes.
		 **/
		private function calculateDuration(){
			if($this->end === NULL || $this->end === ''){
				if(!is_numeric($this->duration))
					$this->duration = 30;

				return $this->duration; 
			}

			$startObj = new \DateTime($this->start);
			$endObj = new \DateTime($this->end);

			//Use absolute difference and remove any decimals.
			return floor(abs($startObj->getTimestamp() - $endObj->getTimestamp())/60);
		}

		/**
		 * Creates and returns the URL for Google Calendar.
		 * Requires that event data is already set using eventData().
		 *
		 * @return string URL to add to Google Calendar. 
		 **/
		private function google(){
			$startTime = $this->formatTime($this->start);
			$endTime = $this->calculateEndTime();

			$summary = $this->summary?$this->summary:'';
			$description = $this->description?$this->description:'';
			$location = $this->location?$this->location:'';

			$url =
				'https://www.google.com/calendar/render'.
				'?action=TEMPLATE'.
				'&text='.$summary.
				'&dates=' .$startTime.
				'/' .$endTime.
				'&details='. $description.
				'&location='. $location.
				'&sprop=&sprop=name:'
			;

			return $url;
		}

		/**
		 * Creates and returns the URL for Live Calendar.
		 * Requires that event data is already set using eventData().
		 *
		 * @return string URL to add to Live Calendar. 
		 **/
		private function live(){

			$startTime = $this->formatTime($this->start);
			$endTime = $this->calculateEndTime();

			$summary = $this->summary?$this->summary:'';
			$description = $this->description?$this->description:'';
			$location = $this->location?$this->location:'';

			$url =
				'https://bay02.calendar.live.com/calendar/calendar.aspx?rru=addevent'.
				'&dtstart='.$startTime.
				'&dtend='.$endTime.
				'&summary='.$summary.
				'&location='.$location.
				'&description='.$description
			;

			return $url;
		}

		/**
		 * Creates and returns the URL for Yahoo Calendar.
		 * Requires that event data is already set using eventData().
		 * Yahoo requires duration in hhmm format.
		 *
		 * @return string URL to add to Yahoo Calendar. 
		 **/
		private function yahoo(){
			$startTime = $this->formatTime($this->start);
			$duration = $this->calculateDuration();

			//Convert the duration to hhmm format. 
			//Prepend a 0 if its a single digit.
			$minute = $duration%60 < 10 ? '0'.$duration%60 : $duration%60;
			$duration = floor($duration/60);
			$hour = $duration < 10 ? '0'.$duration : $duration;

			$yahooDuration = $hour.$minute;

			$summary = $this->summary?$this->summary:'';
			$description = $this->description?$this->description:'';
			$location = $this->location?$this->location:'';

			$url =
				'http://calendar.yahoo.com/?v=60&view=d&type=20'.
				'&title=' .$summary.
				'&st=' .$startTime.
				'&dur=' .$yahooDuration.
				'&desc=' .$description.
				'&in_loc=' .$location;

			return $url;
		}

		/**
		 * Generates event vCards (.ics files) for use with other providers.
		 * Requires that event data is already set using eventData().
		 * 
		 * If generate_url is not empty, the data is appended to it as query strings.
		 * The generator should create and start the vCard download.
		 * 
		 * If generate_url is empty, the link attempts to use inline vCard download.
		 * Note that this won't work inside emails. 
		 *
		 * @param string $generate_url URL to the vCard generator.
		 * @return string The URL for vCard format. 
		 **/
		private function ics($generate_url){
			$startTime = $this->formatTime($this->start);
			$endTime = $this->calculateEndTime();

			$summary = $this->summary?$this->summary:'';
			$description = $this->description?$this->description:'';
			$location = $this->location?$this->location:'';

			$organizerName = $this->organizer?$this->organizer:'';
			$organizerEmail = $this->organizer_email?$this->organizer_email:'';

			//The organizer details are added only if name and email is specified.
			if($organizerName != '' && $organizerEmail != '')
				$organizer_line = "ORGANIZER;CN=" . $organizerName . ":MAILTO:". $organizerEmail . "%0A";
			else
				$organizer_line = '';

			if(!$generate_url) {
				$url = 
					"data:text/calendar;charset=utf8,".
					"BEGIN:VCALENDAR"."%0A".
					"VERSION:2.0"."%0A".
					"PRODID:-//clusterdev.com/addToCalendar v1.0//EN"."%0A".
					"BEGIN:VEVENT"."%0A".
					"DTSTAMP:". $startTime ."%0A".
					"DTSTART:". $startTime ."%0A".
					"DTEND:" . $endTime ."%0A".
					$organizer_line .
					"STATUS:CONFIRMED"."%0A".
					"SUMMARY:" . $summary ."%0A".
					"DESCRIPTION:" . $description ."%0A".
					"LOCATION:" . $location ."%0A".
					"END:VEVENT"."%0A".
					"END:VCALENDAR"
				;
			}else{
				$url = $generate_url."?startTime=$startTime&endTime=$endTime&summary=$summary&description=$description&location=$location&organizer=$organizerName&organizer_email=$organizerEmail";
				return $url;
			}

			return $url;
		}

		/**
		 * Link to the ics() function. 
		 **/
		private function ical($generate_url){
			return $this->ics($generate_url);
		}

		/**
		 * Link to the ics() function. 
		 **/
		private function outlook($generate_url){
			return $this->ics($generate_url);
		}

		/**
		 * Link to the ics() function. 
		 **/
		private function other($generate_url){
			return $this->ics($generate_url);
		}

	}
}
