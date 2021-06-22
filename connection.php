<?php
function read_file($file){
	$string = file_get_contents($file);
	$json_a = json_decode($string, true);
	return $json_a;
}

function write_file($file, $notices){
    $json = json_encode($notices);
    file_put_contents($file, $json);
}

function get_notices($file){
	return read_file($file)['Notices'];
}

function get_today_notices(){
	$notices = array();
	$all_notices = get_notices("notices.json");
    foreach($all_notices as $notice){
        if(display_type($notice) == 0){
			array_push($notices, $notice);
		}
    }
    unset($notice);
	return $notices;
}


function get_all_notices(){
	$all_notices = get_notices("notices.json");
    foreach($all_notices as &$notice){
		$notice['Display'] = display_type($notice);
	}
    unset($notice);
    for ($i = 1;$i < count($all_notices);$i++){
        //insertion sort for ordering notices by display type
        $z = $i;
        while ($z > 0 and $all_notices[$z-1]['Display'] > $all_notices[$z]['Display']){
            $temp = $all_notices[$z-1];
            $all_notices[$z-1] = $all_notices[$z];
            $all_notices[$z] = $temp;
            $z = $z - 1;
            
        }
    }
	return $all_notices;
}


function archive_notice($noticeID){
    $archived_notices = read_file("archive.json");
    $current_notices = get_notices("notices.json");
    foreach($current_notices as $notice){
        if ($notice['NoticeID'] == $noticeID){
            array_push($archived_notices['Notices'], $notice);
        }
    }
    unset($notice);
    write_file("archive.json", $archived_notices);
}


function get_notice($noticeID){
    $current_notices = get_notices("notices.json");
    foreach($current_notices as $notice){
        if ($notice['NoticeID'] == $noticeID){
            return $notice;
        }
    }
    unset($notice);
}


function remove_notice($file, $noticeID){
    $notices = read_file($file);
    for ($i = 0; $i < count($notices['Notices']);$i++){
        if ($notices['Notices'][$i]['NoticeID'] == $noticeID){
            array_splice($notices['Notices'], $i, 1);
        }
    }
    write_file($file, $notices);
}


function hold($noticeID){
    $notices = read_file("notices.json");
    foreach($notices['Notices'] as &$notice){
        if ($notice['NoticeID'] == $noticeID){
            if ($notice['Hold'] != ""){
                $notice['Hold'] = False;
            }else{
                $notice['Hold'] = date("Y-m-d");
            }
        }
    }
    write_file("notices.json", $notices);
}


function archive_old(){
    $notices = read_current();
    for ($i = 0; $i < count($notices['Notices']);$i++){
        if (new Datetime($notices['Notices'][$i]['EndDate']) < new Datetime()){
            archive_notice($notices['Notices'][$i]['NoticeID']);
            remove_notice("notices.json", $notices['Notices'][$i]['NoticeID']);
        }
    }
}


function add_notice($title, $description, $teacher, $initialDate, $endDate, $repeata, $tags ){
    $notices = read_file("notices.json");
    for ($i = 0; $i < count($notices['Notices']);$i++){
        if ($notices["Notices"][$i]['Title'] == $title and $notices["Notices"][$i]['Description'] == $description){
            return;
        }
    }
    $new_notice = array("NoticeID"=>$notices['nextID'], "Title"=> $title, 
    "Description"=> $description, "Teacher"=> $teacher,"InitialDate"=> $initialDate,
    "EndDate"=> $endDate, "Repeata"=> $repeata, "tags"=>$tags, "Hold"=> false);
    $notices['nextID'] += 1;
    array_push($notices['Notices'], $new_notice);
    write_file("notices.json", $notices);
}


function display_type($notice){
    $start = date("Y-m-d", strtotime($notice['InitialDate']));
    $end = date("Y-m-d", strtotime($notice['EndDate']));
    $now = date("Y-m-d");

	if ($end < $now){return 3;}

	if ($notice['Hold'] != ""){return 1;}

	if ($notice['Repeata'] == "once"){
		if ($start == $now){return 0;}
	}

	if ($start <= $now and $end >= $now){
		if ($notice['Repeata'] == "daily"){
			return 0;
		}
		if ($notice['Repeata'] == "weekly" and date("D",strtotime($notice['InitialDate'])) == date('D')){
			return 0;
		}
	}
	return 2;
}
?>