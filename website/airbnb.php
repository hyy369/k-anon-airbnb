<?php
session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <title>Airbnb x Jana</title>
  <meta name="Author" content="Yangyang He">
  <meta content="width=device-width,initial-scale=1" name=viewport>
  <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/airbnb.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script src="https://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
  <!-- <script src="assets/javascript/md5.js"></script> -->
  <!-- <script src="assets/javascript/search.js"></script> -->
  
</head>
<body>

  <div class="jumbotron jumbotron-fluid center">
    <p class="attribution" style="text-align: left;">Photo by <a href="https://unsplash.com/photos/Gk0MAP8A7Cw">Kelcie Gene Papp</a> on <a href="https://unsplash.com/">Unsplash</a></p>
    <div class="container">

      <!-- <img id="icon-marvel-logo-svg" src="assets/img/marvel.svg"/> -->
      <h1 class="display-3">Airbnb x Jana</h1>
      <p class="lead">Blind Deal Finder</p>

      <div class="search">
        <!-- <input type="text" class="search_box" id="title_box" placeholder="Type to start..."> -->
        <form action="airbnb.php" method="post">
            <select class="dropdown" name="state" id="state">
                <option value="WA">Seattle Area</option>
                <option value="MA">Boston Area</option>
                <option value="CA">Los Angeles Area</option>
            </select>
            <script type="text/javascript">
                document.getElementById('state').value = "<?php if ($_POST['state']) echo $_POST['state']; else echo 'WA';?>";
            </script>

            <select class="dropdown" name="accommodates" id="accommodates">
                <option value="0">Number of People</option>
            <?php
                for ($i = 1; $i <=16; $i++){
                echo "<option value=$i>$i</option>";
                }
            ?>
            </select>
            <script type="text/javascript">
                document.getElementById('accommodates').value = "<?php if ($_POST['accommodates']) echo $_POST['accommodates']; else echo '0';?>";
            </script>

            <select class="dropdown" name="beds" id="beds">
            <option value="0">Number of Beds</option>
            <?php
                for ($i = 1; $i <=16; $i++){
                echo "<option value=$i>$i</option>";
                }
            ?>
            </select>
            <script type="text/javascript">
                document.getElementById('beds').value = "<?php if ($_POST['beds']) echo $_POST['beds']; else echo '0';?>";
            </script>
            
            <br>
            <br>
            <!-- <input type="submit"> -->
            <button class="button" id="query_button" type="submit">Search</button>
        </form>

      </div>


    </div>
  

    <div class="container" id="result_header">
    </div>

    <div class="container" id="result">
        <?php 
            function queryDB($query_string) {
                $request = '{"query" : "' . $query_string . '","username": "jana"}';
                // Get cURL resource
                $ch = curl_init();
                // Set some options - we are passing in a useragent too here
                curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => 'http://35.229.60.23:4003/query',
                CURLOPT_POST => 1,
                CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
                CURLOPT_POSTFIELDS => $request,
                ]);
                // Send the request & save response to $resp
                $resp = curl_exec($ch);
                // Close request to clear up some resources
                curl_close($ch);
                return $resp;
            }

            function displayTooFew() {
                echo "<h2>Too few to show</h2>";
            }
            // echo $_POST["state"];
            // echo $_POST["accommodates"];
            // echo $_POST["beds"];
            if ($_POST["state"]) {
                $query_string = 'SELECT id FROM airbnb WHERE state=' . "'".$_POST["state"]."'" . ' AND accommodates>' . $_POST["accommodates"]. ' AND beds>' . $_POST["beds"] . ';';

                $resp = queryDB($query_string);
                $response_json = json_decode($resp, true);

                if (sizeof($response_json['rows']) < 3) {
                    displayTooFew();
                } else {
                    $random_idx = array_rand($response_json['rows'], 1);
                    $id = $response_json['rows'][$random_idx][0];
                    // echo $id;


                    $query_string = 'SELECT * FROM airbnb WHERE id='.$id.';';
                    $resp = queryDB($query_string);
                    $response_json = json_decode($resp, true);
                    $real_row = $response_json['rows'][0];
                    
                    $original_clauses = 'state=' . "'".$_POST["state"]."'" . ' AND accommodates>' . $_POST["accommodates"]. ' AND beds>' . $_POST["beds"];
                    
                    $query_string = 'SELECT * FROM airbnb WHERE ';

                    $clauses_array = array(
                        " AND minimum_nights = $real_row[23]",
                        " AND neighbourhood = '$real_row[7]'",
                        " AND property_type = '$real_row[12]'",
                        " AND bathrooms = $real_row[15] AND half_bathroom = $real_row[16]",
                        " AND beds = $real_row[18]",
                        " AND accommodates >= $real_row[14]",
                        " AND price <= ".(floor($real_row[20]/100)+1)*100,
                        " AND bedrooms = $real_row[17]",
                        " AND guests_included = $real_row[21]",
                        " AND review_scores_rating >= ".floor($real_row[25]/10)*10,
                        " AND review_scores_accuracy >= $real_row[26]",
                        " AND review_scores_cleanliness >= $real_row[27]",
                        " AND review_scores_checkin >= $real_row[28]",
                        " AND review_scores_communication >= $real_row[29]",
                        " AND review_scores_location >= $real_row[30]",
                        " AND review_scores_value >= $real_row[31]",
                        " AND host_response_rate >= ".floor($real_row[3]/10)*10,
                        " AND host_response_time = '$real_row[2]'",
                        " AND bed_type = '$real_row[19]'",
                        " AND cancellation_policy = '$real_row[32]'",
                        " AND room_type = '$real_row[13]'",
                        " AND host_acceptance_rate = $real_row[4]",
                        " AND host_is_superhost = ". ($real_row[5]?'true':'false'),
                        " AND host_identity_verified = ". ($real_row[6]?'true':'false'),
                        ($real_row[22]==0 ? " AND extra_people = 0" : " AND extra_people >0"),
                    );
                    
                    $size = 1;
                    $drop = 0;
                    while ($size <= 3 and $drop <=sizeof($clauses_array)) {
                        $query_string .= $original_clauses;
                        for ($i=$drop; $i<=sizeof($clauses_array); $i++) {
                            $clause = $clauses_array[$i];
                            $query_string .= $clause;
                        }
                        $query_string .= ";";
                        // echo "$query_string<br>";
                        $resp = queryDB($query_string);
                        // echo $resp;
                        $response_json = json_decode($resp, true);
                        $size = sizeof($response_json['rows']);
                        // echo "<br>$size $drop<br>";
                        $drop += 1;
                        $query_string = 'SELECT * FROM airbnb WHERE ';
                    }
                    // echo $drop;
                    



                    echo "<h2>You have found a secret listing!</h2>";
                    echo "<p>";
                    if ($drop <= 18)
                    echo "This host typically responds $real_row[2].<br>";
                    if ($drop <= 17)
                        echo "This host has a response rate of " . floor($real_row[3]/10)*10 . "+%<br>";
                    if ($drop <= 22)
                    echo "This host has an acceptance rate of $real_row[4]%.<br>";
                    if ($real_row[5] and $drop <= 23)
                        echo "This host is a Super Host.<br>";
                    if ($real_row[6] and $drop <= 24)
                        echo "This host has a verified identity.<br>";
                    if ($drop <= 2)
                        echo "This listing is located in $real_row[7].<br>";
                    if ($drop <= 3)
                    echo "This listing is a(n) $real_row[12].<br>";
                    if ($drop <= 21)
                    echo "You get a(n) $real_row[13].<br>";
                    if ($drop <= 6)
                    echo "This listing accommodates up to $real_row[14].<br>";
                    if ($drop <= 4)
                        echo "This listing has ". ($real_row[15]+$real_row[16]*0.5). " bathroom(s)<br>";
                    if ($drop <= 8)
                        echo "This listing has $real_row[17] bedrooms.<br>";
                    if ($drop <= 5)
                        echo "And $real_row[18] beds.<br>";
                    if ($drop <= 19)
                        echo "Bed type is $real_row[19].<br>";
                    if ($drop <= 7)
                        echo "Daily price is less than ".(floor($real_row[20]/100)+1)*100 . " USD.<br>";
                    if ($drop <= 9)
                        echo "Daily price includes $real_row[21] people.<br>";
                    if ($drop <=25 and $real_row[22] == 0)
                        echo "Extra people stays free.<br>";
                    if ($drop <=25 and $real_row[22] > 0)
                        echo "Extra people have to pay.<br>";
                    if ($drop <= 1)
                        echo "Miminum $real_row[23]-night stay required.<br>";
                    if ($drop <= 10)
                        echo "This listing has a review score of " . floor($real_row[25]/10)*10 . "+.<br>";
                    if ($drop <= 11)
                        echo "Accuracy: $real_row[26]<br>";
                    if ($drop <= 12)
                        echo "Cleanliness: $real_row[27]<br>";
                    if ($drop <= 13)
                        echo "Check-in: $real_row[28]<br>";
                    if ($drop <= 14)
                        echo "Communication: $real_row[29]<br>";
                    if ($drop <= 15)
                        echo "Location: $real_row[30]<br>";
                    if ($drop <= 16)
                        echo "Value: $real_row[31]<br>";
                    if ($drop <= 20)
                        echo "Cancellation policy is $real_row[32].<br>";
                    echo "<h4>There are $size listings like this one in this area!</h4>";
                    echo "<form action='show.php' method='post'><button class='button' id='query_button' type='submit'>Reveal this listing</button></form>";
                    echo "</p>";
                    session_unset(); 
                    $_SESSION['id'] = $id; // store session data
                }
                


            } 
            // session_destroy();
        ?>
    </div>
  </div>

  
</body>
</html>
