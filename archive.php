<?php
	session_start();

	require("connection.php"); //includes the file containing all the "database" connection methods
	require("UsefulFunctions.php"); //includes the file containing some useful functions

	if (!(isset($_SESSION['loggedIn']))){ //if new session creates loggedIn variable
		$_SESSION['loggedIn'] = False;
	}


	if (isset($_POST['page_action'])) {
        switch ($_POST['page_action']){
            case 'Login':
                $_SESSION['loggedIn'] = validate_password($_POST['password']);
                break;

		    case 'Logout':
			    $_SESSION['loggedIn'] = False;
                break;

		    case "Remove":
                remove_notice($_POST['remove_noticeID'], "data/archive.json");
                break;
        }
	}
    $loggedin = $_SESSION['loggedIn'];
?>
<html lang="en">
	<head>
		<title>DHS Daily Bulletin</title>
		<link rel="stylesheet" href="Bulletin.css">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
		<meta charset="UTF-8">
	</head>
	<body>
		
		<?php 
            require("html-lib/login_form.php"); //add login / logout form
            require("html-lib/version.html");   //add bulletin verion
            require("html-lib/heading.php");    //add the heading
		?>
        <h2>Archive</h2>
		<?php
            $archived_notices = read_notices("data/archive.json"); //call get_archived() from connection.php for notices
            
            if (count($archived_notices) > 0){ //if there are notices
                echo "<table border=2><tr>";// open the table
                echo "<th id='title'>Title</th>
                      <th id='description'>Description</th>
                      <th id='teacher'>Teacher</th>
                      <th id='dates'>Display Dates</td>";//add the titles of the columns
            
                if ($loggedin){//add the extra column headers if loggedin
                    echo "<th id='arch-action'>Action</th>";
                }
                echo "</tr>";
                foreach ($archived_notices as $row){//for each notice
                    echo "<tr>";
                    echo '<td id="title">'.htmlify($row['Title']);//add all the information for the notices
                        echo "<div class='hide'>".implode(",",$row['Tags'])."</div></td>"; //add the tags of the notice for js to read
                    echo '<td id="description">'.htmlify($row['Description'])."</td>";
                    echo '<td id="teacher">'.htmlify($row['Teacher'])."</td>";
                    echo "<td id='dates'>";
                    echo format_shown_dates($row["InitialDate"], $row["EndDate"], $row["Repeata"])."</td>";
                    if ($loggedin){
                        echo '<td id="arch-action">                        
                            <form method="POST" action="/archive.php" class="action_form" onsubmit="return confirm(\'Are you sure you want to remove this notice forever\')">
                                <input name="remove_noticeID" value='.$row["NoticeID"].' class="hide">
                                <input type="submit" name="page_action" value="Remove" class="action_button">
                            </form>
                        </td>';
                    }
                    echo "</tr>";//close the row
                }
                unset($row);
                echo "</table>"; //close the table
                
                $tags = get_all_tags_from($archived_notices);//get the tags for all displayed notices
                echo "<ul class='tag_list'>";
                foreach ($tags as $tag){ //create list of all tags
                    echo "<li><label><input class='tag_input' name='".$tag."'  type='checkbox' onchange='update()' />".$tag."</label></li>";
                }
                echo "</ul>";
                
            }else{//no notices
                echo "<h2>No notices here</h2>";
            }
		?>
        <h2><br>Head back <a href='/'>here</a></h2>
        <script src="scripts/tag_update.js"></script>
	</body>
	<script>//script to remove page history
		if ( window.history.replaceState ) {window.history.replaceState( null, null, window.location.href );}
	</script>
</html>