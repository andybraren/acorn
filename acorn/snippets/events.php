<div id="calendar-container">
	<div id="header-row">
		<div class="day-column weekend">Sun</div>
		<div class="day-column">Mon</div>
		<div class="day-column">Tue</div>
		<div class="day-column">Wed</div>
		<div class="day-column">Thu</div>
		<div class="day-column">Fri</div>
		<div class="day-column weekend">Sat</div>
	</div>
	<div id="calendar-body">
  	
<?php
  

  //This puts the day, month, and year in seperate variables
  $date = time();
  $day = date('d', $date); // 22
  $month = date('m', $date); // 02
  $year = date('Y', $date); // 2016
  
  $first_day = mktime(0,0,0,$month, 1, $year); // UNIX timestamp of first second of the month
  
  //This gets us the month name
  $title = date('F', $first_day);
  $title = date('F',mktime(0,0,0,$month,1,$year));
  //echo $title;
  
  // Determine how many blank days based on the day of the week the first day of the month falls on
  $day_of_week = date('D', $first_day);
  switch($day_of_week){
    case "Sun": $blank = 0; break;
    case "Mon": $blank = 1; break;
    case "Tue": $blank = 2; break;
    case "Wed": $blank = 3; break;
    case "Thu": $blank = 4; break;
    case "Fri": $blank = 5; break;
    case "Sat": $blank = 6; break;
  }
  
  $days_in_month = cal_days_in_month(0, $month, $year);
  //echo $days_in_month;
  
  // CREATE BLANK DAYS BEFORE
  $day_count = 1;
  while ($blank > 0) {
    //echo '<div class="day-column weekend not-in-month" data-date="2015-11-29"><span class="day-number">29</span></div>';
    $blank = $blank-1;
    $day_count++;
		echo '
		<div class="day-column not-in-month" data-date="2015-11-30">
			<span class="day-number"></span>
		</div>
    ';
  }
  
  // CREATE EACH DAY ONE BY ONE
  $day_num = 1;
  //count up the days, untill we've done all of them in the month
  while ($day_num <= $days_in_month) {
		echo '<div class="day-column" data-date="2015-12-01"><span class="day-number">' . $day_num . '</span>';
		
		$thedate = $year . "-" . $month . "-" . $day_num;
		foreach($site->page('events')->children()->filterBy('StartDate','*=',$thedate)->sortBy('StartDate','desc') as $event) {
      echo '<a href="' . $event->url() . '" class="cal-event">' . $event->title() . '</a>';
    }
    
		echo '</div>';
    $day_num++;
    $day_count++;
    //Make sure we start a new row every week
    if ($day_count > 7) {
      //echo "</tr><tr>";
      //break;
      $day_count = 1;
    }
  }
  
  // CREATE BLANK DAYS AFTER
  while ($day_count > 1 && $day_count <= 7) {
		echo '
		<div class="day-column not-in-month" data-date="2015-11-30">
			<span class="day-number"></span>
		</div>
    ';
    $day_count++;
  }
  
  echo '</div></div>';
?>

<!--

<div class="day-column weekend not-in-month" data-date="2015-11-29">
	<span class="day-number">29</span>
</div>

<div class="day-column not-in-month" data-date="2015-11-30">
	<span class="day-number">30</span>
</div>

<div class="day-column" data-date="2015-12-01">
	<span class="day-number">1</span>
</div>

<div class="day-column" data-date="2015-12-02">
	<span class="day-number">2</span>
</div>

<div class="day-column" data-date="2015-12-03">
	<span class="day-number">3</span>
</div>

<div class="day-column" data-date="2015-12-04">
	<span class="day-number">4</span>
</div>

<div class="day-column weekend" data-date="2015-12-05">
	<span class="day-number">5</span>
</div>

<div class="day-column weekend" data-date="2015-12-06">
	<span class="day-number">6</span>
	<div class="cal-event" data-days="7">Test Event 5</div>
</div>

<div class="day-column" data-date="2015-12-07">
	<span class="day-number">7</span>
</div>

<div class="day-column" data-date="2015-12-08">
	<span class="day-number">8</span>
</div>

<div class="day-column" data-date="2015-12-09">
	<span class="day-number">9</span>
</div>

<div class="day-column" data-date="2015-12-10">
	<span class="day-number">10</span>
</div>

<div class="day-column" data-date="2015-12-11">
	<span class="day-number">11</span>
</div>

<div class="day-column weekend" data-date="2015-12-12">
	<span class="day-number">12</span>
</div>

<div class="day-column weekend" data-date="2015-12-13">
	<span class="day-number">13</span>
</div>

<div class="day-column" data-date="2015-12-14">
	<span class="day-number">14</span>
</div>

<div class="day-column" data-date="2015-12-15">
	<span class="day-number">15</span>
</div>

<div class="day-column" data-date="2015-12-16">
	<span class="day-number">16</span>
</div>

<div class="day-column" data-date="2015-12-17">
	<span class="day-number">17</span>
</div>

<div class="day-column" data-date="2015-12-18">
	<span class="day-number">18</span>
</div>

<div class="day-column weekend" data-date="2015-12-19">
	<span class="day-number">19</span>
</div>

<div class="day-column weekend" data-date="2015-12-20">
	<span class="day-number">20</span>
</div>

<div class="day-column" data-date="2015-12-21">
	<span class="day-number">21</span>
</div>

<div class="day-column" data-date="2015-12-22">
	<span class="day-number">22</span>
	<div class="cal-event" data-days="3">Test Event 3</div>
	<div class="cal-event">Test Event 1</div>
</div>

<div class="day-column today" data-date="2015-12-23">
	<span class="day-number">23</span>
	<div class="cal-event">Test Event 2</div>
</div>

<div class="day-column" data-date="2015-12-24">
	<span class="day-number">24</span>
	<div class="cal-event">Test Event 4</div>
</div>

<div class="day-column" data-date="2015-12-25">
	<span class="day-number">25</span>
</div>

<div class="day-column weekend" data-date="2015-12-26">
	<span class="day-number">26</span>
</div>

<div class="day-column weekend" data-date="2015-12-27">
	<span class="day-number">27</span>
</div>

<div class="day-column" data-date="2015-12-28">
	<span class="day-number">28</span>
</div>

<div class="day-column" data-date="2015-12-29">
	<span class="day-number">29</span>
</div>

<div class="day-column" data-date="2015-12-30">
	<span class="day-number">30</span>
</div>

<div class="day-column" data-date="2015-12-31">
	<span class="day-number">31</span>
</div>

<div class="day-column not-in-month" data-date="2016-1-01">
	<span class="day-number">1</span>
</div>

<div class="day-column weekend not-in-month" data-date="2016-1-02">
	<span class="day-number">2</span>
</div>
</div>
</div>

<iframe src="https://calendar.google.com/calendar/embed?showTitle=0&amp;showDate=0&amp;showPrint=0&amp;showTabs=0&amp;showCalendars=0&amp;height=400&amp;wkst=1&amp;bgcolor=%23FFFFFF&amp;src=1flln7a6nj87uj2fkg1jivd3bjjo5k0k%40import.calendar.google.com&amp;color=%23711616&amp;ctz=America%2FNew_York" style="border-width:0" width="800" height="400" frameborder="0" scrolling="no"></iframe>

-->