<?php
//notice format
//{"NoticeID":int,
// "Title": String,
// "Description": String,
// "Teacher": String,
// "InitialDate":String(yyyy-mm-dd),
// "EndDate":String(yyyy-mm-dd),
// "Repeata": String("once", "daily", "weekly").
// "Tags": List[String],
// "Hold": bool/String(yyyy-mm-dd)   //false when not held, string-date for date to be held on
//}

//notice File Format
// List[Notices] - Notice format above

function read_notices($file){
	$string = file_get_contents($file);
	$json_a = json_decode($string, true);
	return $json_a;
}

function write_notices($notices, $file){
    $json = json_encode($notices);
    file_put_contents($file, $json);
}

function useID(){
    $id = intval(file_get_contents("data/nextId.txt"));
    file_put_contents("data/nextId.txt", $id + 1);
    return $id;
}

function get_today_notices(){
	$notices = array();
	$all_notices = read_notices("data/notices.json");
    foreach($all_notices as $notice){
        if(display_type($notice) == 0){
			array_push($notices, $notice);
		}
    }
    unset($notice);
	return $notices;
}


function get_all_notices(){
	$all_notices = read_notices("data/notices.json");
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


function move_notice($noticeID, $fromFile, $toFile){
    $toNotices = read_notices($toFile);
    $fromNotices = read_notices($fromFile);
    foreach ($toNotices as $notice){
        if ($notice['NoticeID'] == $noticeID){
            array_push($toNotices['Notices'], $notice);
        }
    }
    unset($notice);
    write_notices($fromNotices, "data/archive.json");
    remove_notice($noticeID, $fromFile);

}


function get_notice($noticeID){
    $notices = read_notices("data/notices.json");
    foreach($notices as $notice){
        if ($notice['NoticeID'] == $noticeID){
            return $notice;
        }
    }
    unset($notice);
}


function remove_notice($noticeID, $file){
    $new_notices = array();
    $notices = read_notices($file);
    foreach($notices as $notice){
        if ($notice['NoticeID'] != $noticeID){
            array_push($new_notices, $notice);
        }
    }
    unset($notice);
    write_notices($new_notices, $file);
}


function hold($noticeID){
    $notices = read_notices("data/notices.json");
    foreach($notices as &$notice){
        if ($notice['NoticeID'] == $noticeID){
            if ($notice['Hold'] != ""){
                $notice['Hold'] = False;
            }else{
                $notice['Hold'] = date("Y-m-d");
            }
        }
    }
    write_notices($notices, "data/notices.json");
}


function archive_old(){
    $notices = read_notices("data/notices.json");
    for ($i = 0; $i < count($notices);$i++){
        if (new Datetime($notices[$i]['EndDate']) < new Datetime()){
            move_notice($notices[$i]['NoticeID'], "data/notices.json", "data/archive.json");
            remove_notice($notices[$i]['NoticeID'], "data/notices.json");
        }
    }
}


function add_notice($title, $description, $teacher, $initialDate, $endDate, $repeata, $tags ){
    $notices = read_notices("data/notices.json");
    foreach($notices as $notice){
        if ($notice['Title'] == $title and $notice['Description'] == $description){
            return;
        }
    }
    unset($notice);
    $new_notice = array("NoticeID"=>useID(), 
                        "Title"=> $title, 
                        "Description"=> $description, 
                        "Teacher"=> $teacher,
                        "InitialDate"=> $initialDate,
                        "EndDate"=> $endDate, 
                        "Repeata"=> $repeata, 
                        "Tags"=>$tags, 
                        "Hold"=> false);
    array_push($notices, $new_notice);
    write_notices($notices, "data/notices.json");
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