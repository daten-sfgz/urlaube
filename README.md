# Urlaube
displays calendar data from public calendar

Demo:
https://daten.sfgz.ch/urlaube.php

Help: 
https://daten.sfgz.ch/urlaube.php?help=1

Start / Install:
In a new file or on bottom of urlaube:

include_once("urlaube.php"); // only if in a other file

$cls = new CalendarList();

echo $cls->main();

die();
