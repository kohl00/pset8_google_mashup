<?php

    require(__DIR__ . "/../includes/config.php");

    // numerically indexed array of places
    $places = [];

    // TODO: search database for places matching $_GET["geo"]
    
    if( $_SERVER["REQUEST_METHOD"] == "GET"){
      
      $search_for = $_GET["geo"];
      
        if(preg_match('/^[0-9]{5}/',$search_for)){
            $ary = $search_for;
            $rows = query("SELECT `id`, `country_code`, `postal_code`, `place_name`, `admin_name1`, `admin_code1`, `admin_name2`, `admin_code2`, `admin_name3`, `admin_code3`, `latitude`, `longitude`, `accuracy` FROM `places` WHERE postal_code LIKE '%$search_for%'");
        }
        if(preg_match_all('/[\s,]/',$search_for) || preg_match('/[a-zA-Z -]+$/',$search_for)){
           $ary = preg_split('/[\s,]/',$search_for);
           $ary = array_filter(array_map('trim', $ary));
           $new = array_values($ary);
           
           if(count($new) == 1){
             $town = $new[0];
             $rows = query("SELECT `id`, `country_code`, `postal_code`, `place_name`, `admin_name1`, `admin_code1`, `admin_name2`, `admin_code2`, `admin_name3`, `admin_code3`, `latitude`, `longitude`, `accuracy` FROM `places` WHERE place_name LIKE '%$town%'");     
           }
           if(count($new) >= 2){
            
            $two_word_place = ( !(empty($new[0])) || !(empty($new[1])) ) ? $new[0] . " " . $new[1] : "0" ;
            $two_word_state = ( !(empty($new[1])) && !(empty($new[2])) ) ? $new[1] . " " . $new[2] : $new[1]; //place is $new[0]
            $four_word_place = $two_word_place;
            $four_word_state = ( !(empty($new[2])) && !(empty($new[3])) ) ? $new[2] . " " . $new[3] : "0" ;
                    //search for (two-word "town") OR (one word town AND two word state) OR
                    //$rows = query("SELECT `id`, `country_code`, `postal_code`, `place_name`, `admin_name1`, `admin_code1`, `admin_name2`, `admin_code2`, `admin_name3`, `admin_code3`, `latitude`, `longitude`, `accuracy` FROM `places` WHERE (place_name LIKE '%$two_word_place%') OR (place_name LIKE '%$new[0]%' AND admin_code1 LIKE '%$new[1]%') OR (place_name LIKE '%$new[0]%' AND admin_code1 LIKE '%$two_word_state%') OR (place_name LIKE '%$four_word_place%' AND admin_code1 LIKE '%$four_word_state%')");     
                    $rows = query("SELECT `id`, `country_code`, `postal_code`, `place_name`, `admin_name1`, `admin_code1`, `admin_name2`, `admin_code2`, `admin_name3`, `admin_code3`, `latitude`, `longitude`, `accuracy` FROM `places` WHERE (place_name LIKE '%$four_word_place%' AND admin_code1 LIKE '%$four_word_state%') OR (place_name LIKE '%$new[0]%' AND admin_code1 LIKE '%$two_word_state%') OR (place_name LIKE '%$new[0]%' AND admin_code1 LIKE '%$new[1]%') OR (place_name LIKE '%$two_word_place%')");     
                    //$rows = query("SELECT `id`, `country_code`, `postal_code`, `place_name`, `admin_name1`, `admin_code1`, `admin_name2`, `admin_code2`, `admin_name3`, `admin_code3`, `latitude`, `longitude`, `accuracy` FROM `places` WHERE admin_code1 LIKE '%$new[1]%' AND place_name LIKE '%$town%'");
           }
          }
        foreach($rows as $row){
          array_push($places,$row);
        }
    // output places as JSON (pretty-printed for debugging convenience)
    header("Content-type: application/json");
    print(json_encode($places, JSON_PRETTY_PRINT));
    }


?>
