# Urlaube
displays calendar data from public calendar

## Demo:
https://daten.sfgz.ch/urlaube.php

## Help: 
https://daten.sfgz.ch/urlaube.php?help=1

## Start / Install:
Write in a new file:

include_once("urlaube.php"); 

$cls = new CalendarList();

echo $cls->main();

---

or on bottom of the file right before the PHP end-tag ?>

$cls = new CalendarList();

echo $cls->main();
?>
