<?php

require 'config.php';



function doPost(String $url, Array $post): Array{
	$opts = array('http' =>
		array(
			'method'  => 'POST',
			'header'  => 'Content-type: application/x-www-form-urlencoded',
			'content' => json_encode($post)
		)
	);
	return doRequest($url,$opts);
}

function doGet(String $url, Array $post): Array{
		$opts = array('http'=> 
			array(
				'method'=>'GET',
				'header'=>"Accept: application/json" ."\r\n".
					 "Authorization: Bearer ".token."\r\n"
 			)
		);
	return doRequest($url,$opts);
}

function doRequest(String $url,Array $opts): Array{
	$context  = stream_context_create($opts);
	$result = file_get_contents(BaseURL.$url, false, $context);
	return json_decode($result,true);
}



$token = doPost('/developer/sign-in', $signin)['token'];

if(empty($token)){
	die('Token kam nicht zurück?');
}
define('token',$token);


$activities = doGet('/activities', $signin);
#print_r($activities );
echo '<form method="POST">';
echo '<select name="activitie">';
foreach($activities['activities'] as $activitie){
	echo '<option value="'.$activitie['id'].'">'.$activitie['name'].'</option>';
}
echo '</select>';



echo 'Start: <input name="startDate" type="date" value="'.date('Y-m-d',strtotime('first day of last month')).'">';
echo 'Ende: <input name="endeDate" type="date" value="'.date('Y-m-d',strtotime('last day of last month')).'">';

echo '<input type="submit"></form>';



if(isset($_POST['startDate'])){
$start = date("Y-m-d\TH:i:s.000",strtotime($_POST['startDate']));
$ende = date("Y-m-d\TH:i:s.000",strtotime($_POST['endeDate']));

$timeentries = doGet('/time-entries/'.$start.'/'.$ende.'', $signin);
$gesamtkostenstunden = 0;	

define('bankName','IBN ING-DIBA');
define('bank','DE74 5001 0517 5422 3081 75');
define('bic','INGDDEFFXXX');
echo '<table>';
echo '<tr>';
echo '<th>'.bankName.'</th>';
echo '<th>Bic</th>';
echo '<th>Zahlungsziel</th>';
echo '<th>Zu Bezahlen (€)</th>';
echo '</tr>';
echo '<tr>';
echo '<td>'.bank.'</td>';
echo '<td>'.bic.'</td>';
echo '<td>'.zahlungsziel.'</td>';
echo '<td>'.$gesamtkostenstunden.'</td>';
echo '</tr>';
echo '</table>';
	echo '<table>
				<tr>
					<th>Pos.</th>
					<th>Beschreibung</th>
					<th>Menge / MT</th>
					<th>Tagessatz(€)</th>
					<th>Betrag (€)</th>
				</tr>';
	$i = 0;
	foreach($timeentries['timeEntries'] as $k => $v){
		if($v['activity']['id'] == $_POST['activitie']){
		
		
				$startedAt = strtotime($v['duration']['startedAt']);
				$stoppedAt = strtotime($v['duration']['stoppedAt']);
				$stunden = round(($stoppedAt-$startedAt)/60/60,2);
				$kostenstunden = stundenlohn*$stunden;
				$gesamtkostenstunden += $kostenstunden;
				if(empty($v['note']['text'])){
					$v['note']['text'] = emptyText;
				}
			echo '<tr>';
				echo '<td>'.++$i.'</td>';
				echo '<td>'.$v['note']['text'].'<br> am '.date('d.m.Y',$startedAt).'</td>';
				echo '<td>'.$stunden.' h</td>';
				echo '<td>'.(money_format('%i', stundenlohn*8)).'</td>';;
				echo '<td>'.(money_format('%i', $kostenstunden)).'</td>';;
				
				

		
			echo '</tr>';
		}
			
	}
	echo '</table>';
	echo '<table>';
	echo '<tr><td>Zwischensumme</td><td>'. (money_format('%i', $gesamtkostenstunden)).'</td></tr>';
	echo '<tr><td>Mehrwertsteuersatz</td><td>'. mehrwertsteuersatz.'%</td></tr>';
	echo '<tr><td>Mehrwertsteuer</td><td>'. (money_format('%i', $gesamtkostenstunden/100*19)).'%</td></tr>';
	echo '<tr><td><b>Rechnungsbetrag</b></td><td>'. (money_format('%i', $gesamtkostenstunden+($gesamtkostenstunden/100*19))).'</td></tr>';
	
	echo '</table>';
	
	echo 'Lieferdatum ist gleich Rechnungsdatum.<br>Vielen Dank für Ihren Auftrag!';
}