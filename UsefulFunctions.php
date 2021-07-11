<?php
function htmlify($string){
	$string = str_replace("\n","<br>",$string);//new line fix
    $parts = explode("*", $string); //bold inbetween stars
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

    //link formatting - replace www.youtube.com with <a href="www.youtube.com">www.youtube.com</a>
    //To be honest idk how it works
    $replacePattern1 = "/(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/im";
    $replacedText = preg_replace($replacePattern1, '<a href="$1" target="_blank">$1</a>', $string);

    $replacePattern2 = "/(^|[^\/])(www\.[\S]+(\b|$))/im";
    $replacedText = preg_replace($replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>', $replacedText);

    $replacePattern3 = "/(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/im";
    $replacedText = preg_replace($replacePattern3, '<a href="mailto:$1">$1</a>', $replacedText);

	return $replacedText;
}

function format_shown_dates($start, $end, $repeata){
	if ($repeata == "weekly"){
		$day = date("l",strtotime($start));
	}else{
		$day = "Day";
	}
	
	if ($repeata == "once"){
        return "On ".$start ."<br>Once";
	}else{
		return "From " .$start ."<br>To ".$end."<br> Every ".$day;
	}
}

function get_all_tags_from($notices){
	$tags = array();
    foreach ($notices as $notice){
        foreach($notice['Tags'] as $tag){
            if (!in_array($tag, $tags)){
                array_push($tags, $tag);
            }
        }
        unset($tag);
    }
    unset($notice);
	return $tags;
}

function validate_password($inputPassword){
    $password = fopen("data/password.txt", "r"); //open password file
    $truePassword = fread($password,filesize("data/password.txt"));
    fclose($password);

    if(hash("sha256", $inputPassword) == $truePassword){ //compare password
        return true;
    }else{
        return false;
    }
}
?>