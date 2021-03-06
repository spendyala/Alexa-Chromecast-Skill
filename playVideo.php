<?php
    
    $db = new mysqli('MYSQL_HOST','MYSQL_USER','MYSQL_PASS','MYSQL_NAME');

    if($_GET['command'] == "sendVideo") {

        $searchString   = $_GET['searchString'];
        $correctString  = str_replace(" ","+",urldecode($searchString));
        $youtubeUrl = "https://www.youtube.com/results?search_query=". $correctString;
        $getHTML        = file_get_contents($youtubeUrl);
        $pattern        = '/<a href="\/watch\?v=(.*?)"/i';

        if(preg_match($pattern, $getHTML, $match)){
                $videoID    = $match[1];
        } else {
                echo "Something went wrong!";
                exit;
        }

        $videoTitle = file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=".$videoID."&key=YOUTUBE_API_KEY&fields=items(id,snippet(title),statistics)&part=snippet,statistics");

        if ($videoTitle) {
            $json = json_decode($videoTitle, true);

            $title = $json['items'][0]['snippet']['title'];
        } else {
            $title = "video";
        }


        $query = "INSERT INTO  `commands` (`command` ,`slot`) VALUES ('play',  '$videoID')";
        $run = mysqli_query($db, $query);

        if($run) {
            echo "$title was added Successfully";
        } else {
            echo "The video could not be added.";
        }
    }
    
    if($_GET['command'] == "connectToChromeCast" && !isset($_GET['chromecast'])) {
        $query = "SELECT friendly_name FROM saved_chromecasts WHERE `active` = 1";
        $run = mysqli_query($db, $query);

        if($run) {
            print "Successful - ";
            while ($row=mysqli_fetch_row($run)) {
                $chromecast_list[] = $row[0];
            }
        } else {
            echo "The command could not be added.";
        }
        foreach ($chromecast_list as $row) {
            print $row . " - ";
        }
    } elseif (isset($_GET['chromecast'])) {
        $chromecast_name = mysqli_real_escape_string($db, $_GET['chromecast']);
        $query = "INSERT INTO  `commands` (`command`, `slot`) VALUES ('connectToChromeCast', '$chromecast_name')";
        $run = mysqli_query($db, $query);

        if($run) {
            echo "Command was added Successfully";
        } else {
            echo "The command could not be added.";
        }
    }

    if($_GET['command'] == "resume"){
        $query = "INSERT INTO  `commands` (`command`, `slot`) VALUES ('resume', 'none')";
        $run = mysqli_query($db, $query);

        if($run) {
            echo "Command was added Successfully";
        } else {
            echo "The command could not be added.";
        }
    }

    if($_GET['command'] == "pause"){
        $query = "INSERT INTO  `commands` (`command`, `slot`) VALUES ('pause', 'none')";
        $run = mysqli_query($db, $query);

        if($run) {
            echo "Command was added Successfully";
        } else {
            echo "The command could not be added.";
        }
    }

    if($_GET['command'] == "clearQueue"){
        $query = "TRUNCATE TABLE  `commands`";
        $run = mysqli_query($db, $query);

        if($run) {
            echo "Command was added Successfully";
        } else {
            echo "The command could not be added.";
        }
    }

    if($_GET['command'] == "volume"){
        $volume = $_GET['vol'];
        $query = "INSERT INTO  `commands` (`command`, `slot`) VALUES ('volume', '$volume')";
        print $query;
        $run = mysqli_query($db, $query);

        if($run) {
            echo "Command was added Successfully";
        } else {
            echo "The command could not be added.";
        }
    }
    
?>