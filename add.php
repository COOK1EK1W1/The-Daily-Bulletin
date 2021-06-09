<?php 
    session_start();
    if (!$_SESSION['loggedIn']){//if not logged in
        echo "nah mate";
        die();
    }
    require("connection.php");
    require("UsefulFunctions.php");

    $notice = NULL; //set notice to POST notice for editing
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST['notice'])){
            $notice = get_notice($_POST['notice']);
        }
    }
    if ($notice == NULL){//get default notice if no post
        $notice = array("NoticeID"=>"1", "Title"=> "", 
        "Description"=> "", "Teacher"=> "","InitialDate"=> date("Y-m-d"),
        "EndDate"=> date("Y-m-d"), "Repeata"=> "once", "tags"=>[]);
    }

?>
<html>
    <head>
        <title>DHS Daily Bulletin</title>
        <link rel="stylesheet" href="Bulletin.css">
        <meta charset="UTF-8">
        <script src="update.js"></script>
        <script src="validate_notice.js"></script>
    </head>
    <body>
        <?php require("html/heading.php")?>

        <form method="POST" action="/" onsubmit="return validate_notice()" id="add_form">

            <div id='information'>
                <h2>Information</h2>
                <label for="Title">Enter Notice Title:</label> <br>
                <input name="Title" value='<?=$notice['Title']?>' required maxlength="30"/> <br><br>

                <label for='Description'>Enter Description:</label><br>
                <textarea name='Description' required rows="4" cols="50" maxlength="20000"><?=$notice['Description']?></textarea><br><br>

                <label for="Teacher">Enter Teacher:</label><br>
                <input name="Teacher" value="<?=$notice['Teacher']?>" required maxlength="26" onchange="on_text_update()" /><br><br>
            </div>
            <div id='displayDates'>
                <h2>Display Dates</h2>

                <label for="start_date">Select start date:</label>
                <input type="date" name="start_date" value="<?=$notice['InitialDate']?>" id="start_date" required onchange="on_date_update();" />
                <br>
                <container id="enddatecontainer">
                    <label for="end_date">Select end date:</label>
                    <input type="date" name="end_date" value="<?=$notice['EndDate']?>" id="end_date" required onchange="on_date_update();" />
                </container><br>


                <label for="repeat">Choose how often you want notices to be displayed:</label>

                <select name="repeat" id="repeat" required onchange="on_date_update();">
                
                <option value="once" <?php if ($notice['Repeata'] == "once") echo "selected"?>>Once</option>

                <option value="daily" <?php if ($notice['Repeata'] == "daily") echo "selected"?>>Daily</option>
                
                <option value="weekly" <?php if ($notice['Repeata'] == "weekly") echo "selected"?>>Weekly</option>

                </select><br>
            </div>
            
            <div id='tags'>
                <br>
                <h2>Tags</h2>
                <textarea name="tags" placeholder="Tag1, Tag2, Tag3" id="tag_text"><?=implode(", ", $notice['tags'])?></textarea>
                <ul class='tag_list'>
                    <?php
                        $notices = get_current();
                        $tags = get_tags($notices);
                        foreach($tags as $tag){
                            echo "<input type='button' value='".$tag."' onclick='add_to_tags(\"".$tag."\")'>";
                        }
                        unset($tag);
                    ?>
                </lu>
            </div>
            <?php
                if (isset($_POST['notice'])){
                    echo "<input name='Remove' value=".$_POST['notice']." class='hide'>";
                }
            ?>
            <br>
            <hr>
            <div id='viewer'>
                <h2>Viewer</h2>
                <table border=2>
                    <tr>
                        <th id='title'>Title</th>
                        <th id='description'>Description</th>
                        <th id='teacher'>Teacher</th>
                    </tr>
                    <tr>
                        <td id='title'></th>
                        <td id='description'></th>
                        <td id='teacher'></th>
                    </tr>
                </table>

                <ul class='tag_list'>
                </ul>
                <p id='date_text'></p>
                

            </div>
            <br>
            <input type="submit" name="page_action" value="Add" id="buttons"/>
        </form>
    </body>
    <script>
        on_date_update();
        setInterval(on_text_update, 30);
    </script>
</html>