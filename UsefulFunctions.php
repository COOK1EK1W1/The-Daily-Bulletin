<?php
function htmlify($string){
	$string = str_replace("\n","<br>",$string);
    $parts = explode("*", $string);
    $bold = false;
    $string = "";
    for ($i = 0; $i<count($parts);$i++){
        $string .= $parts[$i];
        if ($i != count($parts) - 1){
            if ($bold){
                $string .= "</b>";
            }else{
                $string .= "<b>";
            }
        }
        $bold = !$bold;
    }
	return $string;

}

function format_shown_dates($start, $end, $repeata){
	if ($repeata == "weekly"){
		$day = date("l",strtotime($start));
	}else{
		$day = "Day";
	}
	
	if ($repeata != "once"){
		return "From " .$start ."<br>To ".$end."<br> Every ".$day;
	}else{
		return "On ".$start ."<br>Once";
	}
}

function get_tags($notices){
	$tags = array();
	for ($i = 0; $i < count($notices);$i++){
		for ($e = 0;$e < count($notices[$i]['tags']);$e++){
			if (!in_array($notices[$i]['tags'][$e], $tags)){
				array_push($tags, $notices[$i]['tags'][$e]);
			}
		}
	}
	return $tags;
}
?>