<?
/**  
	 to start with own configured flexConf
	 create the new Object with an Array
	 instead to start it with no arguments: 

	 $conf['debugMode'] = 1; 
	 $conf['cals']['semester'][0] = 'urlaub';  
	 $conf['cals']['calendar'][0] = 'urlaub';  
	 $cls = new CalendarList($conf);
	 
	 @licence MIT Free Software Licence
	 @author Daniel Rueegg
	 @date 2014-2020
**/

class CalendarList {
	
	/** $this->flexConf **/
	/** boldCriteria = [ 1 | 2 | 4  | 8! ] **/
	/** 	1 = Fett/Mager-class nur jene, die mit dem in 'useFba' definierten Besetzt-Status versehen sind **/
	/** 	2 = ganztaegige Termine, die laenger als 1 Tag dauern **/
	/** 		muss ev. mit Fett/Mager-class aus Besetzt-Status ueberschrieben werden. In 'useFba' definiert **/
	/** 	4 = Fett jene, bei welchen im Feld 'name' ein in 'boldIfKeyword' hinterlegtes Schluesselwort vorhanden ist **/
	/** 		muss ev. mit Fett/Mager-class aus Besetzt-Status ueberschrieben werden. In 'useFba' definiert **/
	/** 	8 = Fett jene, bei welchen Feld 'name' genau einem in 'boldIfKeyword' hinterlegten Schluesselwort entspricht **/
	/** 		kann mit Fett/Mager-class aus Besetzt-Status ueberschrieben werden. In 'useFba' definiert **/
	/** useFba[bold]+[normal] = [ F | T! | B | O! ] 'F' = Frei, 'T' = vorläufig(temporaer) normal!, 'B' = Besetzt, 'O' = ausser_Haus(outdoor) bold! **/
	/** boldIfKeyword[0...n] = 'ferien' **/

	Public $bool2num = array(false=>0,true=>1);
	Public $calDB         = array();
	Public $req      = array( 
	'help'=>'help' , 
	'y'=>'selYear' , 
	'yd'=>'yearDifference' , 
	'charset'=>'charset' , 
	'py'=>'yearsPassed', 
	'jv'=>'yearsPassed' , 
	'fy'=>'yearsFuture' , 
	'jz'=>'yearsFuture' , 
	'bld'=>'boldCriteria' , 
	'bdy'=>'onlyBody' , 
	'sem'=>'useSemesterCals' , 
	'fix'=>'fix'  , 
	'd'=>'debugMode' , 
	'c'=>'cache' , 
	'ct'=>'cachingtime' , 
	'crop'=>'croplength' ,
	'cpos'=>'croppedpos' ,
	'wday'=>'cropweekdays' ,
	'ht'=>'hideThis' ,
	'ho'=>'hideOthers'
	);
	Public $flexConf = array(
		  'cal_url'		=>	'https://subdomain.mydomain.ch/subpath/calendarname@calenardomain.ch/',
		  'cals'		=> array( 'calendar'=>array( 0=>'urlaub') ) ,
		  'Semestercals'=> array( 'semester'=>array( 0=>'urlaub') , 'calendar'=>array( 0=>'urlaub') ) ,
		  'useSemesterCals'	=> 0 ,
		  'cache'	=> 'on' ,
		  'cachingtime'	=> 12 ,
		  'cachedir'	=> 'cached_files/' ,
		  'debugMode'	=> 0 ,
		  'fix'			=> 1 ,
		  'delNotag'	=> 1 ,
		  'alwaysFullHtQuery'=> 0 ,
		  'yearsPassed'	=> 1 ,
		  'yearsFuture'	=> 2 ,
		  'dateOfChange'=> array( 'day'=>21 , 'month'=>7 ) ,
		  'dateOfHalfyear'=> array( 'day'=>1 , 'month'=>8 ) ,
		  'Monatsnamen'=> array( 'Januar','Februar','M&auml;rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember' ) ,
		  'Tagesnamen'=> array( 'Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag' ) ,
		  'yearLinkFormat' => 'Schuljahr' ,
		  'hyLabel'		=> array('Herbstsemester','Fr&uuml;hlingssemester') ,
		  'hyKeyWords'	=> array('Sommerferien','Sportferien') ,
		  'boldCriteria'=> 8 ,
		  'hideThis'=> '',
		  'hideOthers'=> 'F',
		  'specialDates'=> 'B',
		  'useFba'=> array( 'bold'=>'O', 'normal'=>'T' ) ,
		  'boldIfKeyword'=> array( 'Sommerferien', 'Herbstferien', 'Weihnachtsferien', 'Sportferien', 'Frühlingsferien','Herbstsemester','Frühlingssemester' ) ,
		  'tabledesign'	=> array('width1'=>'25%','width2'=>'','width3'=>'' , 'cellpadding' => '3' ) , 
		  'croplength'	=> 23 , 
		  'croppedpos'	=> 1 , 
		  'cropweekdays' => 2 , 
		  'cropweekdayschar'=> array( false=>',' , true=>'.' ) ,
		  'cssFiles'	=> '' , 
		  'jsHead'	=> '' ,
		  'onlyBody'	=> 0,
		  'charset'	=> 'UTF-8' ,
		  'formName'	=> 'filter',
		  'objName'		=> array('date'=>'own_date') ,
		  'aktYear'		=> '' , 
		  'selYear'		=> '' , 
		  'yearDifference'	=> 0 , 
		  'timNow'		=> '' ,
		  'yearNavi'		=> '' ,
		  'planBody'		=> '' ,
		  'cssFontPath'		=> '/fonts/' ,
		  'dottedlineImage' => 'background-image: url(https://daten.sfgz.ch/typo3conf/ext/mffdesign/Resources/Public/images/design/dot.png); background-repeat:repeat-x;background-position:bottom;',
		  'cssHead'		=> '
				body{white-space:nowrap;overflow:hidden;padding:0;margin:0;color:#000;letter-spacing:0.01em;font-family:Helvetica, arial,sans-serif;font-weight:normal;font-style:normal;}
				div.navi0{padding:0;margin:0;font-size:smaller;float:left;width:auto;border:0px solid #f77; }
				div.navi1{padding:0 5px;font-size:smaller;float:left;width:auto;border:1px solid #f77;}
				div.cont {font-size:smaller;float:left;width:481px;}
				div.cont H1{margin:0 0 2px 0;color:#666;font-size:15px;}
				div.cont H2{margin:0 0 5px 0;font-size:10pt;}
				div.cont H3{margin:13px 0 3px 0;font-size:12px;}
				TABLE {width:481px;padding:0;margin-bottom:10px;}
				TABLE TH, TABLE TD {padding:2px 3px 1px 0;font-size:12px;vertical-align:top;text-align:left;border-bottom:0px dotted #888;}
				TABLE TH{}
				TABLE TR.headrow TH{padding:8px 0 0 0;color:#666;font-weight:bold;border-bottom:2px solid #888;}
				TABLE TD{font-size:12px;vertical-align:top;border-bottom:0px dotted #888;}
				TABLE TR.noBorderBottom TD{border-bottom:0;padding:0;}
				TABLE TD P {margin:0;}
				TD.allDay{font-weight:bold;}
				TD.first{ }
				TD.second{}
				TR.notBoldclass TD.first{font-weight:normal;}
				TR.boldclass TD{font-weight:bold;letter-spacing:0.02em;}
				UL.contentnav {background-image: url(https://daten.sfgz.ch/typo3conf/ext/mffdesign/Resources/Public/images/design/dot.png); background-repeat:repeat-x;background-position:top;font-weight:normal;font-family:Helvetica, Arial, sans-serif;letter-spacing:0;font-size:12px;lign-height:16px;padding:0;margin:0 10px 0 0px;width:158px;border:0;border-top:0px dotted #888;list-style-type:none;}
				UL.contentnav LI {padding:3px 0 2px 0;border-bottom:0px dotted #888;}
				UL.contentnav LI A {display:block;}
				UL.contentnav LI A, UL.contentnav LI A:visited {color:black;text-decoration:none;}
				UL.contentnav LI A:hover {color: rgb(204, 51, 0);}
				UL.contentnav LI A.active{color: rgb(204, 51, 0);}
			  ' , 
		);

	function __construct( $conf=array() ) {
	  foreach(array_keys($conf) as $ak){if(array_key_exists( $ak , $this->flexConf )) $this->flexConf[$ak] = $conf[$ak];}
	  $this->init();
	  $this->mkCalendarDB();
	  $this->flexConf['yearNavi']= $this->htmlYearLinks();
	  if($this->flexConf['onlyBody']==3 || $this->flexConf['onlyBody']==5){
	      $this->flexConf['planBody'] = $this->htmlVeranstaltungenListe();
	  }else{
	      $this->flexConf['planBody'] = $this->htmlKalenderListe();
	  }
	}

	function main() {
	  $nocache='<meta http-equiv="cache-control" content="no-cache"/>';
	  $nocache.='<meta http-equiv="Pragma" content="no-cache"/>';
	  $title =  date( 'd. M Y' ) ;
	  $content_encoded = ($this->flexConf['charset']!='UTF-8') ? iconv( 'UTF-8' , $this->flexConf['charset'] , $this->flexConf['planBody']) : $this->flexConf['planBody'] ; 
	  $mainBodyText ='
		  <div class="cont debug'.$this->bool2num[ $this->flexConf['debugMode']==1].'" id="mainContainer">
			  <div>
			  '.$content_encoded.'
			  </div>
		  </div>';
	  if(isset($_REQUEST['help']) && $_REQUEST['help']!=='0'){
		$bodyText.= $this->help();
	  }elseif(isset($_REQUEST['info']) && $_REQUEST['info']!=='0'){
		$bodyText.= 'Parameter &info gibt es in "Urlaube" nicht, versuche <a href="?help=1">&help</a> ';
	  }elseif($this->flexConf['onlyBody']==1){
		$bodyText = $mainBodyText;
		$bodyText.=' <div style="clear:both;">'.$this->flexConf['debugText'].'</div>';
	  }elseif($this->flexConf['onlyBody']==2){
		$bodyText = '<div class="navi0">'.$this->flexConf['yearNavi'].'</div>';
		$bodyText.=' <div style="clear:both;">'.$this->flexConf['debugText'].'</div>';
	  }elseif($this->flexConf['onlyBody']==3){
		$bodyText = $mainBodyText;
		$bodyText.=' <div style="clear:both;">'.$this->flexConf['debugText'].'</div>';
	  }elseif($this->flexConf['onlyBody']==4){
		return $content_encoded;
	  }elseif($this->flexConf['onlyBody']==5){
		return $content_encoded;
	  }elseif(empty($this->flexConf['onlyBody'])){
		$bodyText = '<div class="navi0">'.$this->flexConf['yearNavi'].'</div>';
		$bodyText.= $mainBodyText;
		$bodyText.=' <div style="clear:both;">'.$this->flexConf['debugText'].'</div>';
	  }
	  
	  return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html;charset='.$this->flexConf['charset'].'">
			'.$nocache.'
			<title>
				Urlaube '.$title.'
			</title>
			'.$this->flexConf['cssFiles'].'
			'.$this->flexConf['jsHead'].'
			<style type="text/css"><!--
			'.str_replace( '__cssFontPath__' , $this->flexConf['cssFontPath'] , $this->flexConf['cssHead']).'
			--></style>
		</head>
		  <body>
		  '.$bodyText.'
		  </body>
		</html>
	  ';
	}

	function htmlYearLinks() {
		if(!is_array($this->calDB['_years_']))return;
		asort($this->calDB['_years_']);
		foreach($this->calDB['_years_'] as $sj){
		  if($this->flexConf['aktYear']-$this->flexConf['yearsPassed']>$sj)continue;
		  if($this->flexConf['aktYear']+$this->flexConf['yearsFuture']<$sj)continue;
		  $reqArr['y'] = $sj;
		  $linkText = $this->flexConf['yearLinkFormat'].' '.$sj.'/'.($sj-1999);
		  $theLink = ' <li style="'.$this->flexConf['dottedlineImage'].'"><a href="'.$this->mkLinkQuery($reqArr).'">'.$linkText.'</a></li> ';
		  $selLink = ' <li style="'.$this->flexConf['dottedlineImage'].'"><a class="active" href="'.$this->mkLinkQuery($reqArr).'">'.$linkText.'</a></li> ';
		  $linkArr=array(false=>$theLink,true=>$selLink);
		  $yearLink .= ''.$linkArr[ $this->flexConf['selYear'] == $sj ].'';
		}
		return '<ul class="contentnav">'.$yearLink.'</ul>';
	}

	function help() {
		$lnk_y=  '<a href="'.$this->mkLinkQuery(array('y'=>'2011')).'">2011</a>';
		$lnk_yd0 =  '<a href="'.$this->mkLinkQuery(array('yd'=>'0')).'">0</a>';
		$lnk_ydv2 =  '<a href="'.$this->mkLinkQuery(array('yd'=>'-2')).'">-2</a>';
		$lnk_ydn2 =  '<a href="'.$this->mkLinkQuery(array('yd'=>'2')).'">2</a>';
		$lnk_py0 =  '<a href="'.$this->mkLinkQuery(array('py'=>'0')).'">0</a>';
		$lnk_py9 =  '<a href="'.$this->mkLinkQuery(array('py'=>'9')).'">9</a>';
		$lnk_fy0 =  '<a href="'.$this->mkLinkQuery(array('fy'=>'0')).'">0</a>';
		$lnk_fy9 =  '<a href="'.$this->mkLinkQuery(array('fy'=>'9')).'">9</a>';
		$lnk_ =  '<a href="'.$this->mkLinkQuery(array(''=>'')).'"></a>';
		$lnk_bdy0 =  '<a href="'.$this->mkLinkQuery(array('bdy'=>'0')).'">0</a>';
		$lnk_bdy1 =  '<a href="'.$this->mkLinkQuery(array('bdy'=>'1')).'">1</a>';
		$lnk_bdy2 =  '<a href="'.$this->mkLinkQuery(array('bdy'=>'2')).'">2</a>';
		$lnk_bdy3 =  '<a href="'.$this->mkLinkQuery(array('bdy'=>'3')).'">3</a>';
		$lnk_bdy4 =  '<a href="'.$this->mkLinkQuery(array('bdy'=>'4')).'">4</a>';
		$lnk_bdy5 =  '<a href="'.$this->mkLinkQuery(array('bdy'=>'5')).'">5</a>';
		$lnk_chr0 =  '<a href="'.$this->mkLinkQuery(array('charset'=>'UTF-8')).'">UTF-8</a>';
		$lnk_chr1 =  '<a href="'.$this->mkLinkQuery(array('charset'=>'ISO-8859-1')).'">ISO-8859-1</a>';
		$lnk_chr2 =  '<a href="'.$this->mkLinkQuery(array('charset'=>'ISO-8859-15')).'">ISO-8859-15</a>';
		$lnk_wday0 =  '<a href="'.$this->mkLinkQuery(array('wday'=>'2')).'">2</a>';
		$lnk_wday1 =  '<a href="'.$this->mkLinkQuery(array('wday'=>'20')).'">20</a>';
		$lnk_sem0 =  '<a href="'.$this->mkLinkQuery(array('sem'=>'0')).'">0</a>';
		$lnk_sem1 =  '<a href="'.$this->mkLinkQuery(array('sem'=>'1')).'">1</a>';
		$lnk_hlp0 =  '<a href="'.$this->mkLinkQuery(array('help'=>'0')).'">0</a>';
		$lnk_hlp1 =  '<a href="'.$this->mkLinkQuery(array('help'=>'1')).'">1</a>';
		$lnk_fix0 =  '<a href="'.$this->mkLinkQuery(array('fix'=>'0')).'">0</a>';
		$lnk_fix1 =  '<a href="'.$this->mkLinkQuery(array('fix'=>'1')).'">1</a>';
		$lnk_d0 =  '<a href="'.$this->mkLinkQuery(array('d'=>'0')).'">0</a>';
		$lnk_d1 =  '<a href="'.$this->mkLinkQuery(array('d'=>'1')).'">1</a>';
		$lnk_c0 =  '<a href="'.$this->mkLinkQuery(array('c'=>'off')).'">off</a>';
		$lnk_c1 =  '<a href="'.$this->mkLinkQuery(array('c'=>'on')).'">on</a>';
		$lnk_ct0 =  '<a href="'.$this->mkLinkQuery(array('ct'=>'12')).'">12</a>';
		$lnk_ct1 =  '<a href="'.$this->mkLinkQuery(array('ct'=>'1')).'">1</a>';
		$lnk_ct2 =  '<a href="'.$this->mkLinkQuery(array('ct'=>'0.25')).'">0.25</a>';
		$lnk_ho =  '<a href="'.$this->mkLinkQuery(array('ho'=>'')).'">&nbsp;&nbsp;</a>';
		$lnk_ho0 =  '<a href="'.$this->mkLinkQuery(array('ho'=>'F')).'">F</a>';
		$lnk_ho1 =  '<a href="'.$this->mkLinkQuery(array('ho'=>'T')).'">T</a>';
		$lnk_ho2 =  '<a href="'.$this->mkLinkQuery(array('ho'=>'B')).'">B</a>';
		$lnk_ho3 =  '<a href="'.$this->mkLinkQuery(array('ho'=>'O')).'">O</a>';
		$lnk_ht =  '<a href="'.$this->mkLinkQuery(array('ht'=>'')).'">&nbsp;&nbsp;</a>';
		$lnk_ht0 =  '<a href="'.$this->mkLinkQuery(array('ht'=>'F')).'">F</a>';
		$lnk_ht1 =  '<a href="'.$this->mkLinkQuery(array('ht'=>'T')).'">T</a>';
		$lnk_ht2 =  '<a href="'.$this->mkLinkQuery(array('ht'=>'B')).'">B</a>';
		$lnk_ht3 =  '<a href="'.$this->mkLinkQuery(array('ht'=>'O')).'">O</a>';
		$lnk_crop0 =  '<a href="'.$this->mkLinkQuery(array('crop'=>'23')).'">23</a>';
		$lnk_crop1 =  '<a href="'.$this->mkLinkQuery(array('crop'=>'999')).'">999</a>';
		$lnk_cpos0 =  '<a href="'.$this->mkLinkQuery(array('cpos'=>'0')).'">0</a>';
		$lnk_cpos1 =  '<a href="'.$this->mkLinkQuery(array('cpos'=>'1')).'">1</a>';
		$lnk_bld1 =  '<a href="'.$this->mkLinkQuery(array('bld'=>'1')).'">1</a>';
		$lnk_bld2 =  '<a href="'.$this->mkLinkQuery(array('bld'=>'2')).'">2</a>';
		$lnk_bld4 =  '<a href="'.$this->mkLinkQuery(array('bld'=>'4')).'">4</a>';
		$lnk_bld8 =  '<a href="'.$this->mkLinkQuery(array('bld'=>'8')).'">8</a>';
		$fbaTxt=array( 'O'=>'ausser Haus' , 'T'=>'temporaer' , 'B'=>'Besetzt' , 'F'=>'Frei');
		$bodyText .= '<table style="width:600px;">';
		$bodyText .= '<tr>';
		$bodyText .= '<tr><th style="text-align:left;width:50px;">Opt</th><th style="text-align:left;width:350px;">Variable</th><th style="text-align:left;">Werte</th><th style="text-align:left;">Aktuell</th></tr>';
		$bodyText .= '<tr><td>help</td><td>dieser Hilfetext </td><td>[ '.$lnk_hlp0.' | '.$lnk_hlp1.' ] </td><td>1</td></tr>';
		$bodyText .= '<tr><td>y</td><td>Jahr (Startjahr)</td><td>[ '.$lnk_y.'...JJJJ ] </td><td>'.$this->flexConf['selYear'].'</td></tr>';
		$bodyText .= '<tr><td>yd</td><td>Jahr-Differenz (Startjahr) Differenz zu aktuellem Jahr </td><td>[ '.$lnk_ydv2.' ... '.$lnk_yd0.' ... '.$lnk_ydn2.' ] </td><td>'.$this->flexConf['yearDifference'].'</td></tr>';
		$bodyText .= '<tr><td>py,jv</td><td>Anzahl vergangener Jahre in Navigation</td><td>[ '.$lnk_py0.'...'.$lnk_py9.' ] </td><td>'.$this->flexConf['yearsPassed'].'</td></tr>';
		$bodyText .= '<tr><td>fy,jz</td><td>Anzahl kommender Jahre in Navigation</td><td>[ '.$lnk_fy0.'...'.$lnk_fy9.' ] </td><td>'.$this->flexConf['yearsFuture'].'</td></tr>';
		$bodyText .= '<tr><td>bdy</td><td>Alles mit ausgeben [0], nur Tabelle [1], nur Navigation [2], nur spezielle Termine [3], nur inneres HTML f&uuml;r Urlaube [4] oder f&uuml;r spezielle Termine [5] anzeigen</td><td>[ '.$lnk_bdy0.' | '.$lnk_bdy1.' | '.$lnk_bdy2.' | '.$lnk_bdy3.' | '.$lnk_bdy4.' | '.$lnk_bdy5.' ] </td><td>'.$this->flexConf['onlyBody'].'</td></tr>';
		$bodyText .= '<tr><td>charset</td><td>Zeichensatz</td><td>[ '.$lnk_chr0.' ... '.$lnk_chr2.' ] </td><td>'.$this->flexConf['charset'].'</td></tr>';
		$bodyText .= '<tr><td><i>hideOthers</i></td><td> nur diese <i>anzeigen</i>:</td><td>[ '.$lnk_ho.' | '.$lnk_ho0.' | '.$lnk_ho1.' | '.$lnk_ho2.' | '.$lnk_ho3.'] </td><td>['.$this->flexConf['hideOthers'].'] </td></tr>';
		$bodyText .= '<tr><td><i>hideThis</i></td><td> diese <i>verstecken:</i> (nur aktiv wenn <i>hideOthers</i> leer)</td><td>[ '.$lnk_ht.' | '.$lnk_ht0.' | '.$lnk_ht1.' | '.$lnk_ht2.' | '.$lnk_ht3.']</td><td>['.$this->flexConf['hideThis'].'] </td></tr>';
		$bodyText .= '<tr><td>sem</td><td>Verwendet den Kalender "Semester" f&uuml;r die ersten beiden Zeilen (Herbst- und Fr&uuml;hlingssemester) langsam!</td><td>[ '.$lnk_sem0.' | '.$lnk_sem1.' ] </td><td>'.$this->flexConf['useSemesterCals'].'</td></tr>';
		$bodyText .= '<tr><td>fix</td><td>fast/slow (fix=1 schnelle Version gibt alle Eintr&auml;ge aus, falls gar keine mit tag <i>urlaub</i> markiert wurden, fix=0 unterdr&uuml;ckt sie.)</td><td>[ '.$lnk_fix0.' | '.$lnk_fix1.' ] </td><td>'.$this->flexConf['fix'].'</td></tr>';
		$bodyText .= '<tr><td>d</td><td>debugMode</td><td>[ '.$lnk_d0.' | '.$lnk_d1.' ] </td><td>'.$this->flexConf['debugMode'].'</td></tr>';
		$bodyText .= '<tr><td>c</td><td>Wenn cache ausgeschaltet, wird caching-Datei gel&ouml;scht.</td><td>[ '.$lnk_c0.' | '.$lnk_c1.' ] </td><td>'.$this->flexConf['cache'].'</td></tr>';
		$bodyText .= '<tr><td>ct</td><td>Alter in Stunden, welches eine Datei erreichen kann.</td><td>[ '.$lnk_ct0.' ...'.$lnk_ct1.'... '.$lnk_ct2.' ] </td><td>'.$this->flexConf['cachingtime'].'</td></tr>';
		$bodyText .= '<tr><td>crop</td><td>Ab dieser Wortl&auml;nge eine Extrazeile f&uuml;r Text voranstellen.</td><td>[ '.$lnk_crop0.' ... '.$lnk_crop1.' ] </td><td>'.$this->flexConf['croplength'].'</td></tr>';
		$bodyText .= '<tr><td>cpos</td><td>Die Extrazeile unten anh&auml;ngen [1] statt sie voranzustellen [0].</td><td>[ '.$lnk_cpos0.' | '.$lnk_cpos1.' ] </td><td>'.$this->flexConf['croppedpos'].'</td></tr>';
		$bodyText .= '<tr><td>wday</td><td>Wochentag abk&uuml;rzen [2] oder belassen [20].</td><td>[ '.$lnk_wday0.' | '.$lnk_wday1.' ] </td><td>'.$this->flexConf['cropweekdays'].'</td></tr>';
		$bodyText .= '<tr><td>bld</td><td>boldCriteria <br>1=besetzt-status bestimmt, ob fett. aktuell: "'.$this->flexConf['useFba']['bold'].': <i>'.$fbaTxt[$this->flexConf['useFba']['bold']].'</i>"';
		$bodyText .= '<br>Der Buchstabe "'.$this->flexConf['useFba']['bold'].'" ist in "flexConf[ useFba ][ bold ]" definiert: ';
		$bodyText .= '<br><i><u>F</u>rei, <u>T</u>empor&auml;r (provisorisch), <u>B</u>esetzt,  <u>O</u>utdoor (ausser Haus)</i> ';
		$bodyText .= '<br>2=fett wenn mehrt&auml;gig und ganzt&auml;gig.';
		$bodyText .= '<br>4=Schl&uuml;sselwort in Text enthalten.';
		$bodyText .= '<br>8=Schl&uuml;sselwort entspricht dem Text genau.';
		$bodyText .= '<br>Schl&uuml;sselworte in "flexConf[ boldIfKeyword ]" definiert:';
		$bodyText .= '<br><i>'.implode(', ',$this->flexConf['boldIfKeyword']).'</i>.</td><td>[ '.$lnk_bld1.' | '.$lnk_bld2.' | '.$lnk_bld4.' | '.$lnk_bld8.' ] </td><td>'.$this->flexConf['boldCriteria'].'</td></tr>';
		$bodyText .= '</table>';
		if( $this->flexConf['onlyBody']!=1 )$bodyText .= $this->flexConf['debugText'];
		return $bodyText;
	}
	function htmlVeranstaltungenListe( $tableTags = true ) {
	  if(!is_array($this->calDB['data']))return '<h1>kein Kalender</h1>keine Daten';
	  $out='';
	  foreach(array_keys($this->calDB['sort']) as $c){
		foreach(array_keys($this->calDB['sort'][$c]) as $t){
		  if(!is_array($this->calDB['sort'][$c][$t]))continue;
		  foreach(array_keys($this->calDB['sort'][$c][$t]) as $id){
			if( $this->flexConf['selYear']!=$this->calDB['data'][$c][$t][$id]['year'])continue;
			if($this->flexConf['timNow'] > $this->calDB['data'][$c][$t][$id]['xe'])continue;
			if( $this->calDB['data'][$c][$t][$id]['fba'] !=$this->flexConf['specialDates'])continue;
			$criteria['moredays'] = (date('d.m.y' , $this->calDB['data'][$c][$t][$id]['xs']) != date('d.m.y' , $this->calDB['data'][$c][$t][$id]['xe']));
			$out.= '<tr>';
			$out.='<td style="text-align:left;verical-align:top;'.$this->flexConf['dottedlineImage'].'">';
			$out.= $this->calDB['data'][$c][$t][$id]['name'].'';
			$out.= ' </td>';
			$out.='<td style="text-align:left;verical-align:top;width:auto;'.$this->flexConf['dottedlineImage'].'">';
			if($this->calDB['allDay'][$c][$t][$id]){
//				$out.= ' '.date('d.m.Y',$this->calDB['data'][$c][$t][$id]['xs']).' ';
				$out.= substr($this->flexConf['Tagesnamen'][ date('w',$this->calDB['data'][$c][$t][$id]['xs']) ],0,$this->flexConf['cropweekdays']).$this->flexConf['cropweekdayschar'][ $this->flexConf['cropweekdays']<10 ].date(' j. ',$this->calDB['data'][$c][$t][$id]['xs']).$this->flexConf['Monatsnamen'][ date('n',$this->calDB['data'][$c][$t][$id]['xs'])-1 ].date(' Y',$this->calDB['data'][$c][$t][$id]['xs']);
				if(!$criteria['moredays'])$out.= 'ganzer Tag';
			}else{
// 				$out.= ' '.date('d.m.Y',$this->calDB['data'][$c][$t][$id]['xs']).',';
				$out.= substr($this->flexConf['Tagesnamen'][ date('w',$this->calDB['data'][$c][$t][$id]['xs']) ],0,$this->flexConf['cropweekdays']).$this->flexConf['cropweekdayschar'][ $this->flexConf['cropweekdays']<10 ].date(' j. ',$this->calDB['data'][$c][$t][$id]['xs']).$this->flexConf['Monatsnamen'][ date('n',$this->calDB['data'][$c][$t][$id]['xs'])-1 ].date(' Y',$this->calDB['data'][$c][$t][$id]['xs']);
				$out.= ' '.date('H:i',$this->calDB['data'][$c][$t][$id]['xs']).'';
			}
			$out.= '</td>';
			$out.= '<td style="text-align:left;verical-align:top;width:auto;'.$this->flexConf['dottedlineImage'].'">';
			if($criteria['moredays']){
//			  $out.= ' - '.date('d.m.Y',$this->calDB['data'][$c][$t][$id]['xe']);
			  $out.= ' bis '.substr($this->flexConf['Tagesnamen'][ date('w',$this->calDB['data'][$c][$t][$id]['xe']) ],0,$this->flexConf['cropweekdays']).$this->flexConf['cropweekdayschar'][ $this->flexConf['cropweekdays']<10 ].date(' j. ',$this->calDB['data'][$c][$t][$id]['xe']).$this->flexConf['Monatsnamen'][ date('n',$this->calDB['data'][$c][$t][$id]['xe'])-1 ].date(' Y',$this->calDB['data'][$c][$t][$id]['xe']);
			}
			$out.= '</td>';
			$out.='</tr>';
		  }
		}
	  }
	  if(empty($out)) return;
	  $titleRow = '<tr><td colspan="3" style="">';
	  $titleRow.= "&nbsp;\n";
	  $titleRow.= '</td></tr><tr><th class="headrow" colspan="3" style="verical-align:top;text-align:left;'.$this->flexConf['dottedlineImage'].'">Spezielle Termine Grundbildung</th></tr>';
	  if(!$tableTags) return $titleRow.$out;
	  return '<table border="0" cellpadding="3" cellspacing="0">'.$titleRow.$out.'</table>';
	}
	function htmlKalenderListe() {
	  //$intFld = array( 'year', 's' , 'e' , 'name' , 'loc' , 'fr' , 'transp' , 'fba' , 'class' , 'allDay' , 'yearText');
	  if(!is_array($this->calDB['data']))return '<h1>kein Kalender</h1>keine Daten';
	  $adClass=array( false=>'' , true=>'allDay' );
	  $isBold=array( $this->flexConf['useFba']['normal']=>'notBoldclass' , $this->flexConf['useFba']['bold']=>'boldclass' );
	  $dateArr=array(false=>'H.i',true=>'H');
	  $boldStyle=array('allDay'=>'font-weight:bold;',''=>'');
	  if(!$this->flexConf['useSemesterCals']){$lastRowClass=' class="noBorderBottom"';$lastrowTdStyle='';}else{$lastRowClass='';$lastrowTdStyle=' style="'.$this->flexConf['dottedlineImage'].'"';}
	  foreach(array_keys($this->calDB['sort']) as $c){
		foreach(array_keys($this->calDB['sort'][$c]) as $t){
		  if(!is_array($this->calDB['sort'][$c][$t]))continue;
		  foreach(array_keys($this->calDB['sort'][$c][$t]) as $id){
			if( $this->flexConf['selYear']!=$this->calDB['data'][$c][$t][$id]['year'])continue;
			if(!empty($this->flexConf['hideOthers'])){
			  if($this->calDB['data'][$c][$t][$id]['fba'] !=$this->flexConf['hideOthers'])continue;
			}else{
			  if( $this->calDB['data'][$c][$t][$id]['fba'] ==$this->flexConf['hideThis'])continue;
			}
			$namArr=explode(' ',$this->calDB['data'][$c][$t][$id]['name']);
			$isLonger=0;
			foreach($namArr as $nstr){
			  if(strlen($nstr)>$this->flexConf['croplength']){$isLonger=1;break;}
			}
			$criteria['bold'][1] = ( $this->calDB['data'][$c][$t][$id]['fba'] == $this->flexConf['useFba']['bold'] );
			$criteria['bold'][2] = ( $this->calDB['data'][$c][$t][$id]['s']!=$this->calDB['data'][$c][$t][$id]['e'] && $this->calDB['allDay'][$c][$t][$id]==1 );
			$criteria['bold'][4] = ( strpos( ' '.str_replace( $this->flexConf['boldIfKeyword'] , 'boldIfKeyword' , $this->calDB['data'][$c][$t][$id]['name']) , 'boldIfKeyword') > 0 );
			$criteria['bold'][8] = ( str_replace( $this->flexConf['boldIfKeyword'] , 'boldIfKeyword' , ($this->calDB['data'][$c][$t][$id]['name'])) == 'boldIfKeyword' );
			$criteria['moredays'] = (date('d.m.y' , $this->calDB['data'][$c][$t][$id]['xs']) != date('d.m.y' , $this->calDB['data'][$c][$t][$id]['xe']));
			$criteria['allday'] = $this->calDB['allDay'][$c][$t][$id];
			$calCount[$c]+=1;
			$debugFBCode = array(0=>'',1=>''.$this->calDB['data'][$c][$t][$id]['fba'].' ');
			$debugTitleS = array(0=>'',1=>' title="'.$this->calDB['data'][$c][$t][$id]['s'].'"');
			$debugTitleE = array(0=>'',1=>' title="'.$this->calDB['data'][$c][$t][$id]['e'].'"');
			$out = '';
			if($isLonger && !$this->flexConf['croppedpos']){
			  $out .= '<tr class="noBorderBottom '.$isBold[ $this->calDB['data'][$c][$t][$id]['fba'] ].'">';
			  $out.= '<td colspan="3">'.$this->calDB['data'][$c][$t][$id]['name'].'</td>';
			  $out.= '</tr>';
			}
			if( ($isLonger && $this->flexConf['croppedpos']) || !empty($this->calDB['data'][$c][$t][$id]['fr'])){
			    $classname=' noBorderBottom';
			    $tdStyle='';
			}else{
			    $classname='';
			    $tdStyle=''.$this->flexConf['dottedlineImage'].'';
			}
			$out .= '<tr class="'.$isBold[ $this->calDB['data'][$c][$t][$id]['fba'] ].''.$classname.'">';
			$out.= '<td style="vertical-align:top;'.$boldStyle[$adClass[ $criteria['bold'][$this->flexConf['boldCriteria']] ]].'" class="first '.$adClass[ $criteria['bold'][$this->flexConf['boldCriteria']] ].'">';
			$out.= ''.$debugFBCode[ $this->flexConf['debugMode'] ];
			if(empty($isLonger)){
			  $out.= $this->calDB['data'][$c][$t][$id]['name'];
			}
			$out.= '';
			$out.= ' </td><td style="vertical-align:top;" '.$debugTitleS[ $this->flexConf['debugMode'] ].'>';
			$out.= substr($this->flexConf['Tagesnamen'][ date('w',$this->calDB['data'][$c][$t][$id]['xs']) ],0,$this->flexConf['cropweekdays']).$this->flexConf['cropweekdayschar'][ $this->flexConf['cropweekdays']<10 ].date(' j. ',$this->calDB['data'][$c][$t][$id]['xs']).$this->flexConf['Monatsnamen'][ date('n',$this->calDB['data'][$c][$t][$id]['xs'])-1 ].date(' Y',$this->calDB['data'][$c][$t][$id]['xs']);
			$out.= ' </td><td style="vertical-align:top;" '.$debugTitleE[ $this->flexConf['debugMode'] ].'>';
			if( $criteria['moredays'] ){
			  $out.= ' bis '.substr($this->flexConf['Tagesnamen'][ date('w',$this->calDB['data'][$c][$t][$id]['xe']) ],0,$this->flexConf['cropweekdays']).$this->flexConf['cropweekdayschar'][ $this->flexConf['cropweekdays']<10 ].date(' j. ',$this->calDB['data'][$c][$t][$id]['xe']).$this->flexConf['Monatsnamen'][ date('n',$this->calDB['data'][$c][$t][$id]['xe'])-1 ].date(' Y',$this->calDB['data'][$c][$t][$id]['xe']);
			}else{
			  $out.= ' Schuleinstellung';
			  if( $criteria['allday'] ){
				$out.= ' ganzer Tag ';
			  }else{
				$out.= ' ab '.date($dateArr[date('i',$this->calDB['data'][$c][$t][$id]['xs'])=='00'].' \U\h\r ',$this->calDB['data'][$c][$t][$id]['xs']);
			  }
			}
			$out.= ' </td></tr>';
			if( empty($classname) && !empty($tdStyle) ){
			    $out.= '<tr><td colspan="3" style="font-size:1px;height:1px;padding:0;'.$this->flexConf['dottedlineImage'].'">'.$this->calDB['data'][$c][$t][$id]['fr'].'</td></tr>';
			}
			if($isLonger && $this->flexConf['croppedpos']){
			  if(!empty($this->calDB['data'][$c][$t][$id]['fr'])){
			    $classname=' noBorderBottom';
			    $tdStyle='';
			  }else{
			    $classname='';
			    $tdStyle=''.$this->flexConf['dottedlineImage'].'';
			  }
			  $out .= '<tr class="'.$isBold[ $this->calDB['data'][$c][$t][$id]['fba'] ];
			  $out.=''.$classname.'">';
			  $out.= '<td colspan="3" style="'.$tdStyle.'">'.$this->calDB['data'][$c][$t][$id]['name'].'</td></tr>';
			}
			//zusaetzliche Zeile
			if(!empty($this->calDB['data'][$c][$t][$id]['fr'])){
			    $out.= '<tr><td colspan="3" style="'.$this->flexConf['dottedlineImage'].'">'.$this->calDB['data'][$c][$t][$id]['fr'].'</td></tr>';
			}
			$outArr[ $this->calDB['data'][$c][$t][$id]['class'] ][]=$out;
			$xe = $this->calDB['data'][$c][$t][$id]['xe'];
			$bis="\n\t\t<span>".date( 'j. ' , $xe )."".$this->flexConf['Monatsnamen'][date( 'm' , $xe )-1]."".date( ' Y' , $xe )." </span> ";
		  }
		}
		if($calCount[$c]){$lastCal = $c;$outArr[ 'PUB' ][$c]='<tr'.$lastRowClass.'><td colspan="3"'.$lastrowTdStyle.'>&nbsp;</td></tr>';}
	  }
	  $prependText = '';
	  $outList='';
	  if(is_array($outArr)){
		if(!is_array($outArr['PRI']) && isset($lastCal)){
		  unset($outArr[ 'PUB' ][$lastCal]);
		}
		$semesterRowsOut=$this->_htmlKalenderList_semesterRows();
		if(is_array($outArr['PUB'])) {
		    $outList.="\n\t".'<tr class="headrow"><th style="font-size:12px;color:#666;border-bottom:2px solid #888;padding-top:8px;" colspan="3" align="left">Schuljahr '.$this->flexConf['selYear'].'/'.($this->flexConf['selYear']+1).' </th></tr>'.$semesterRowsOut.implode("\n\t" , $outArr['PUB'] ); 
		}
		if(is_array($outArr['PRI'])){
		  $outList.= "\n\t".'<tr><th colspan="3" align="left" style="'.$this->flexConf['dottedlineImage'].'">Schuleinstellungen nur Grundbildung</th></tr>'.implode("\n\t" , $outArr['PRI'] );
		  //$outList.= "\n\t".'<tr'.$lastRowClass.'><td colspan="3"'.$lastrowTdStyle.'>&nbsp;</td></tr>';
		}
	  }else{
		$prependText='leer...';
	  }
	  $spezielleTermine = $this->htmlVeranstaltungenListe( false );
	  for($z=1;$z<=3;++$z){if($this->flexConf['tabledesign']['width'.$z]){$cols.='<col width="'.$this->flexConf['tabledesign']['width'.$z].'">';}else{$cols.='<col>';}}
	  $outTable = '<table border="0" cellpadding="'.$this->flexConf['tabledesign']['cellpadding'].'" cellspacing="0"><colgroup>'.$cols.'</colgroup>'.$outList.$spezielleTermine.'</table>';
	  return '<h1 style="font-size:15px;color:#666;margin:0 0 2px 0;">Ferienregelung und Schuleinstellungen bis '.$bis.' </h1>'.$prependText.$outTable;
	}
	function _htmlKalenderList_semesterRows() {
	  if(is_array($this->hjDB['endsem']) && !is_array($this->calDB['sort']['semester']) ){
		ksort($this->hjDB['endsem']);
		foreach(array_keys($this->hjDB['endsem']) as $sm){
		  $y=$this->flexConf['selYear'];
		  if(!isset($this->hjDB['endsem'][$sm][$y])){continue;}
		  if(isset($this->hjDB['startsem'][$sm][$y])){
			$ab=' '.substr($this->flexConf['Tagesnamen'][ date('w',$this->hjDB['startsem'][$sm][$y]) ],0,$this->flexConf['cropweekdays']).$this->flexConf['cropweekdayschar'][ $this->flexConf['cropweekdays']<10 ].date(' j. ',$this->hjDB['startsem'][$sm][$y]).$this->flexConf['Monatsnamen'][ date('n',$this->hjDB['startsem'][$sm][$y])-1].date(' Y',$this->hjDB['startsem'][$sm][$y]).' ';
		  }else{
			$ab='...';
		  }
		  $debugOut.= '<tr class="boldclass">';
		  $debugOut.= '<td style="font-weight:bold;" class="first allDay">'.$this->flexConf['hyLabel'][$sm].'</td>';
		  $debugOut.= '<td style="font-weight:bold;">'.$ab.'</td>';
		  $debugOut.= '<td style="font-weight:bold;">bis '.substr($this->flexConf['Tagesnamen'][ date('w',$this->hjDB['endsem'][$sm][$y]) ],0,$this->flexConf['cropweekdays']).$this->flexConf['cropweekdayschar'][ $this->flexConf['cropweekdays']<10 ].date(' j. ',$this->hjDB['endsem'][$sm][$y]).$this->flexConf['Monatsnamen'][ date('n',$this->hjDB['endsem'][$sm][$y])-1 ].date(' Y',$this->hjDB['endsem'][$sm][$y]).'</td>';
		  $debugOut.= '</tr>';
		  $debugOut.='<tr><td colspan="3" style="font-size:1px;height:1px;padding:0;'.$this->flexConf['dottedlineImage'].'"></td></tr>';
		}
		//$debugOut.='<tr><td colspan="3" style="border-bottom:1px dotted #888;">&nbsp;</td></tr>';
		$debugOut.='<tr><td colspan="3" style="'.$this->flexConf['dottedlineImage'].'">&nbsp;</td></tr>';
	  }
	  return $debugOut;
	}
	function init( ) {
	  foreach(array_keys($this->req) as $var){
		if( isset($_REQUEST[ $var ]) && isset($this->flexConf[$this->req[$var]]) ){$this->flexConf[$this->req[$var]]=$_REQUEST[ $var ];}
	  }
	  if( $this->flexConf['useSemesterCals']  ){ $this->flexConf['cals'] =  $this->flexConf['Semestercals']; }

	  if( $this->flexConf['timNow'] == 0 )$this->flexConf['timNow'] = mktime( date('H') , date('i') , date('s') ,  date('n') , date('j') , date('Y') );
	  if( date('w' , $this->flexConf['timNow'] ) == 0 ){ $this->flexConf['timNow'] += ( 60 * 60 *24 ); }

	  if( isset($_REQUEST[ 'fix' ]) ){ 
	  	$this->flexConf['fix'] = $this->bool2num[ $_REQUEST['fix'] == 1 ];
	  }
	  $stichtag = $this->flexConf['dateOfChange']['day'] + ($this->flexConf['dateOfChange']['month'] * 100);
	  $heutetag = date('d') + (  date('m')*100 );
	  if( isset(  $_REQUEST['y'] ) ){
		$this->flexConf['selYear'] = $_REQUEST['y'];
	  }else{
		$this->flexConf['selYear'] = date( 'Y' ) - $this->bool2num[ $heutetag<$stichtag ] ;
	  }
	  if( !empty($this->flexConf['yearDifference']) ){
		$this->flexConf['selYear']+=$this->flexConf['yearDifference'];
	  }
	  $wechseltag = $this->flexConf['dateOfChange']['day'] + ($this->flexConf['dateOfChange']['month'] * 100);
	  $this->flexConf['aktYear'] = date( 'Y' ) - $this->bool2num[ $heutetag<$wechseltag ] ;
	}

	function mkCalendarDB() {
	  if($this->flexConf['fix']){
		$this->calDB = $this->_getCalendarData_fast();
	  }else{
		$this->calDB = $this->_getCalendarData_complete();
	  }
	  return;
	}
	function _getCalendarData_fast() {
	  $rawUrl = rtrim( $this->flexConf['cal_url'] , '/') . '/';
	  $timeRange = $this->_mkCalendarData_range() ;
	  foreach( array_keys($this->flexConf['cals']) as $cl){
		if(empty($cl))continue;
		foreach( $this->flexConf['cals'][$cl] as $tg){
		  if(empty($tg))continue;
		  $URL = $rawUrl.$cl.'?fmt=json&query=tag:'.$tg.$timeRange;
		  //$raw = json_decode( file_get_contents( $URL ) , true );
		  $raw = $this->_importCalendarData($cl.$tg.$timeRange,$URL);
		  if(!is_array($raw['appt']))continue;
		  foreach(array_keys($raw['appt']) as $id){
			$kdb = $raw['appt'][$id]['inv'][0]['comp'][0];
			$calsDB['data'][ $cl ][ $tg ][ $kdb['uid'] ] = $kdb;
			$calsDB['data'][ $cl ][ $tg ][ $kdb['uid'] ] = $this->_mkDateTimeFields($kdb );
			$calsDB['_years_'][$calsDB['data'][ $cl ][ $tg ][ $kdb['uid'] ]['year']]=$calsDB['data'][ $cl ][ $tg ][ $kdb['uid'] ]['year'];
			$calsDB['allDay'][ $cl ][ $tg ][ $kdb['uid'] ] = $this->bool2num[ $kdb['allDay'] == 1 ];
			$calsDB['sort'][ $cl ][ $tg ][ $kdb['uid'] ] = $calsDB['data'][ $cl ][ $tg ][ $kdb['uid'] ]['xs'];
		  }
		  $calsDB['sort'][ $cl ][ $tg ] = $this->_sortCalendarData($calsDB['sort'][ $cl ][ $tg ]);
		}
	  }
	  return $calsDB;
	}


	function _getCalendarData_complete() {
	  $rawUrl = rtrim( $this->flexConf['cal_url'] , '/') . '/' ;
	  $timeRange = $this->_mkCalendarData_range() ;
	  foreach( array_keys($this->flexConf['cals']) as $cl){
		$URL = $rawUrl.$cl.'?fmt=json'.$timeRange;
		unset($raw);
		//$raw = json_decode( file_get_contents( $URL ) , true );
		$raw = $this->_importCalendarData($cl.$timeRange,$URL);
		if(is_array($raw['appt'])){
		  foreach(array_keys($raw['appt']) as $id){
			$calsDB['data'][ $cl ][ 'notag' ][ $raw['appt'][$id]['inv'][0]['comp'][0]['uid'] ] = $raw['appt'][$id]['inv'][0]['comp'][0];
		  }
		}
		if(count($this->flexConf['cals'][$cl])>=1){
		  foreach( $this->flexConf['cals'][$cl] as $tg){
			$URL = $rawUrl.$cl.'?fmt=json&query=tag:' . $tg.$timeRange ;
			unset($raw);
			//$raw = json_decode( file_get_contents( $URL ) , true );
			$raw = $this->_importCalendarData($cl.$tg.$timeRange,$URL);
			if( count($raw['appt']) && count($raw['appt']) < count($calsDB['data'][ $cl ][ 'notag' ]) ){
			  foreach(array_keys($raw['appt']) as $id){
				$calsDB['data'][ $cl ][ $tg ][ $raw['appt'][$id]['inv'][0]['comp'][0]['uid'] ] = $raw['appt'][$id]['inv'][0]['comp'][0];
			  }
			}
		  }
		}
	  }
	  if(!is_array($calsDB['data'])){echo "misslungen";return;}
	  foreach( array_keys($calsDB['data']) as $cl){
		if($this->flexConf['delNotag'] == 1){
		  unset($calsDB['data'][ $cl ]['notag']); 
		}elseif( count($calsDB['data'][ $cl ])>1 ){
		  foreach( array_keys($calsDB['data'][ $cl ]) as $tg){
			if( $tg == 'notag' )continue;
			foreach(array_keys($calsDB['data'][ $cl ][ $tg ]) as $id){
			  unset($calsDB['data'][ $cl ]['notag'][$id]); 
			}
		  }
		}
	  }
	  foreach( array_keys($calsDB['data']) as $cl){
		  foreach( array_keys($calsDB['data'][ $cl ]) as $tg){
			  foreach(array_keys($calsDB['data'][ $cl ][ $tg ]) as $id){
				$kdb = $calsDB['data'][ $cl ][ $tg ][$id];
				$calsDB['allDay'][ $cl ][ $tg ][ $kdb['uid'] ] = $this->bool2num[ $kdb['allDay'] == 1 ];
				$calsDB['data'][ $cl ][ $tg ][ $kdb['uid'] ] = $this->_mkDateTimeFields($kdb );
				$calsDB['sort'][ $cl ][ $tg ][ $kdb['uid'] ] = $calsDB['data'][ $cl ][ $tg ][ $kdb['uid'] ]['xs'];
				$calsDB['_years_'][$calsDB['data'][ $cl ][ $tg ][ $kdb['uid'] ]['year']]=$calsDB['data'][ $cl ][ $tg ][ $kdb['uid'] ]['year'];
			  }
			  $calsDB['sort'][ $cl ][ $tg ] = $this->_sortCalendarData($calsDB['sort'][ $cl ][ $tg ]);
		  }
	  }
	  return $calsDB;
	}

	function _mkCalendarData_range() {
	  $montsDifference = 13;
	  $startdate = '&start=m'.($montsDifference*(1+$this->flexConf['yearsPassed'])).'mon';
	  $enddate = '&end=p'.($montsDifference*(1+$this->flexConf['yearsFuture'])).'mon';
	  return $startdate.$enddate;
	}
	function _mkDateTimeFields(  $calsDB ) {
		$kdb = $calsDB;
		if($kdb['allDay']){
		  $datStrAb = $kdb['s'][0]['d'] ;
		  $calsDB['s'] = substr( $datStrAb , 6 , 2) . '.' . substr( $datStrAb , 4 , 2) . '.' . substr( $datStrAb , 2 , 2);
		  $calsDB['xs'] = mktime( 0,0,0, substr( $datStrAb , 4 , 2) , substr( $datStrAb , 6 , 2) , substr( $datStrAb , 2 , 2) );
		  $datStrBis = $kdb['e'][0]['d'] ;
		  $calsDB['e'] = substr( $datStrBis , 6 , 2) . '.' . substr( $datStrBis , 4 , 2) . '.' . substr( $datStrBis , 2 , 2);
		  $calsDB['xe'] = mktime( 23,59,59, substr( $datStrBis , 4 , 2) , substr( $datStrBis , 6 , 2) , substr( $datStrBis , 2 , 2));
		}else{
		  $calsDB['s'] = date ( 'd.m.y H:i',round( $kdb['s'][0]['u'] / 1000  ));
		  $calsDB['xs'] = round( $kdb['s'][0]['u'] / 1000  );
		  $calsDB['e'] = date ( 'd.m.y H:i',round( $kdb['e'][0]['u'] / 1000  ));
		  $calsDB['xe']   = round( $kdb['e'][0]['u'] / 1000  );
		}
		$stichtag = $this->flexConf['dateOfHalfyear']['day'] + ($this->flexConf['dateOfHalfyear']['month'] * 100);
		$heutetag = date('d',$calsDB['xs']) + (  date('m',$calsDB['xs'])*100 );
		$isHerbstText=array( false=>'' , true=>'/'.(1+date('Y',$calsDB['xs'])) );
		$calsDB['yearText'] = $this->flexConf['hyLabel'][ $this->bool2num[ $heutetag<$stichtag ] ].' '.date( 'Y' , $calsDB['xs'] ) . $isHerbstText[ $this->bool2num[ $heutetag>=$stichtag ] ];
		$calsDB['year'] = date( 'Y' , $calsDB['xs'] ) - $this->bool2num[ $heutetag<$stichtag ] ;
		if($this->flexConf['useSemesterCals'])return $calsDB;

		if( trim(strtolower($this->flexConf['hyKeyWords'][0])) == trim(strtolower($calsDB['name'])) ){
		  // Sommerferien
		  $this->hjDB['startsem'][0][date( 'Y' , $calsDB['xs'] )] = $this->_nextWeekDay($calsDB['xe']);
		  $this->hjDB['endsem'][1][date( 'Y' , $calsDB['xs'] )-1] = $this->_lastWeekDay($calsDB['xs']);
		}elseif( trim(strtolower($this->flexConf['hyKeyWords'][1])) == trim(strtolower($calsDB['name'])) ){
		  // Sportferien
		  $this->hjDB['startsem'][1][date( 'Y' , $calsDB['xs'] )-1] = $this->_nextWeekDay($calsDB['xe']);
		  $this->hjDB['endsem'][0][date( 'Y' , $calsDB['xs'] )-1] = $this->_lastWeekDay($calsDB['xs']);
		}
		return $calsDB;
	}
	function encodeContent( $text ) {
	  if( $this->flexConf['charset'] == 'UTF-8') return $text;
	  return iconv( 'UTF-8' , $this->flexConf['charset'] , $text);
	}
	function _importCalendarData($cal,$URL) {
		$t1=explode( ' ' , microtime() );
	  if (!file_exists($this->flexConf['cachedir'])) {mkdir($this->flexConf['cachedir']);}
	  $actFilename = $this->flexConf['cachedir'].$cal.'.txt';
	  if( file_exists($actFilename) ){
		if( ( mktime()-filemtime($actFilename) ) > ($this->flexConf['cachingtime'] * 60 * 60) ){ // alter der Datei max 12 * 60 * 60
		  unlink($actFilename);
		}else{
		  if($this->flexConf['cache'] == 'off'){
			unlink($actFilename);
		  }else{
			$content_utf8 = file_get_contents( $actFilename );
			$raw = json_decode(  $content_utf8 , true );
		  }
		}
	  }
	  if(!isset($raw)){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$content_utf8 = curl_exec($ch);
		curl_close($ch);
		file_put_contents( $actFilename , $content_utf8 );
		$raw = json_decode( $content_utf8 , true );
	  }
	  if($this->flexConf['debugMode']){
		$secOld = (mktime()-filemtime($actFilename));
		if($secOld<60){
		  $this->flexConf['debugText'] .= '<p>'.$cal.' ist '.round( $secOld ).' Sek. alt.</p>';
		}elseif($secOld<3600){
		  $this->flexConf['debugText'] .= '<p>'.$cal.' ist '.round( $secOld/60,1 ).' Min. alt.</p>';
		}else{
		  $this->flexConf['debugText'] .= '<p>'.$cal.' ist '.round( $secOld/3600,1 ).' Std. alt.</p>';
		}
		$this->flexConf['debugText'] .= '<a href="?help='.($_REQUEST['help'] ? 0 : 1).'&d=1">Hilfe ein/aus</a>';//.$this->obj_debugLink($t1);
	  }
	  return $raw;
	}
	function _lastWeekDay($weekenddate) {
	  return $weekenddate-( 1+($this->bool2num[ date('w',$weekenddate)==1 ])*(3600*24));
	}
	function _nextWeekDay($weekenddate) {
	  return $weekenddate+( 1+($this->bool2num[ date('w',$weekenddate)==6 ])*(3600*24));
	}
	function _sortCalendarData($calsDB) {
	  if(is_array($calsDB))asort($calsDB);
	  return $calsDB;
	}

	function mkLinkQuery($overwriteArr=array()) {
	  foreach(array_keys($this->req) as $var){
		if(is_array( $this->req[$var] )){
		  foreach(array_keys($this->req[$var]) as $vbr){
			if(is_array( $this->req[$var][$vbr] )){
			  foreach(array_keys($this->req[$var][$vbr]) as $vcr){
				if(isset($overwriteArr[$var][$vbr][$vcr])){
				  $outArr[$var][$vbr][$vcr]=$overwriteArr[$var][$vbr][$vcr];
				}else{
				  if( isset( $_REQUEST[$var][$vbr][$vcr] ) ){
					$outArr[$var][$vbr][$vcr]=$_REQUEST[$var][$vbr][$vcr];
				  }else{
					if($this->flexConf['alwaysFullHtQuery'] && isset($this->flexConf[ $this->req[$var][$vbr][$vcr] ]))$outArr[$var][$vbr][$vcr]=$this->flexConf[ $this->req[$var][$vbr][$vcr] ];
				  }
				}
			  }
			}else{
			  if(isset($overwriteArr[$var][$vbr])){
				$outArr[$var][$vbr]=$overwriteArr[$var][$vbr];
			  }else{
				if( isset( $_REQUEST[$var][$vbr] ) ){
				  $outArr[$var][$vbr]=$_REQUEST[$var][$vbr];
				}else{
				  if($this->flexConf['alwaysFullHtQuery'] && isset($this->flexConf[ $this->req[$var][$vbr] ]))$outArr[$var][$vbr]=$this->flexConf[ $this->req[$var][$vbr] ];
				}
			  }
			}
		  }
		}else{
		  if(isset($overwriteArr[$var])){
			$outArr[$var]=$overwriteArr[$var];
		  }else{
			if( isset( $_REQUEST[$var] ) ){
			  $outArr[$var]=$_REQUEST[$var];
			}else{
			  if($this->flexConf['alwaysFullHtQuery'] && isset($this->flexConf[ $this->req[$var] ]))$outArr[$var]=$this->flexConf[ $this->req[$var] ];
			}
		  }
		}
	  }
	  if(!is_array($outArr)){return;}
	  foreach(array_keys($outArr) as $var){
		if(is_array($outArr[$var])){
		  foreach(array_keys($outArr[$var]) as $vbr){
			if(is_array($outArr[$var][$vbr])){
			  foreach(array_keys($outArr[$var][$vbr]) as $vcr){
				$outReq[]=$var.'['.$vbr.']['.$vcr.']='.$outArr[$var][$vbr][$vcr];
			  }
			}else{
			  $outReq[]=$var.'['.$vbr.']='.$outArr[$var][$vbr];
			}
		  }
		}else{
		  $outReq[]=$var.'='.$outArr[$var];
		}
	  }
	  if(!is_array($outReq))return;
	  return 'https://'.$_SERVER['SERVER_NAME'] .$_SERVER['PHP_SELF'] .'?'.implode( '&amp;' , $outReq);
	}

	function obj_debugLink($t1) {
	  if(!$this->flexConf['debugMode'] && (!isset($_REQUEST['help']) || $_REQUEST['help']==='0') )return;
	  $fixtext=array( 0=>'slow' , 1=>'fast' );
	  $cachetext=array( false=>'off' , true=>'on' );
		$t2=explode( ' ' , microtime() );
		$timeElapsed = round(1000*(($t2[0]+$t2[1])-($t1[0]+$t1[1]))).'/1000 sec. ';
		$reqArr['fix'] = $this->bool2num[1!=$this->flexConf['fix']];
		$cacheArr['c'] = $cachetext['on'!=$this->flexConf['cache']];
		$LNK = '<a href="'.$this->mkLinkQuery($reqArr).'">&rArr; '.$fixtext[ $this->bool2num[1!=$this->flexConf['fix']] ].'</a>';
		$LNK.= '&nbsp;<a href="'.$this->mkLinkQuery($cacheArr).'">&rArr; cache '.$cachetext[ 'on'!=$this->flexConf['cache'] ].'</a>';
		$helptext=array(false=>'Hilfe',true=>'Hilfe aus');
		$reqHArr['help'] = $this->bool2num[ !isset($_REQUEST['help']) || $_REQUEST['help']==='0' ];
		//$reqHArr['bdy'] = 0;$reqHArr['d'] = 1;
		$LNKH = '<a href="'.$this->mkLinkQuery($reqHArr).'">'.$helptext[isset($_REQUEST['help']) && $_REQUEST['help']!=='0'].'</a>';
	  return '<p>'.$timeElapsed .'<b>'. $fixtext[ $this->flexConf['fix'] ].' cache:'.$this->flexConf['cache'].'</b> ...  '.$LNK.' '.$LNKH.'</p>';
	}


}

?>
