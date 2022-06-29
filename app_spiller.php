<?php 
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
  header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');


  if($_POST) {

    $email = $_POST['email'];
    $consent = $_POST['consent'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $brewing_methods = $_POST['brewing_methods'];
    $colours = $_POST['colours'];
    $flavour_strength = $_POST['flavour_strength'];
    $times_of_day = $_POST['times_of_day'];
    $describe_favourite = $_POST['describe_favourite'];
    $salad_dressings = $_POST['salad_dressings'];
    $flavour_notes = $_POST['flavour_notes'];
    $per_day = $_POST['per_day'];
    $suggestion_1 = $_POST['suggestion_1'];
    $suggestion_1_url = $_POST['suggestion_1_url'];
    $suggestion_1_title = $_POST['suggestion_1_title'];
    $suggestion_1_image = $_POST['suggestion_1_image'];
    $suggestion_2 = $_POST['suggestion_2'];
    $suggestion_2_url = $_POST['suggestion_2_url'];
    $suggestion_2_title = $_POST['suggestion_2_title'];
    $suggestion_2_image = $_POST['suggestion_2_image'];

    if($email) {
        $data = array(
            "api_key" => "pk_6a935db3a6acd08e51e3f93e0bb85bd926",
            "profiles" => array (
              array(
                "email" => $email,
                "consent" => $consent,
                "first_name" => $first_name,
                "last_name" => $last_name,
                "brewing_methods" => $brewing_methods,
                "colours" => $colours,                             
                "flavour_strength" => $flavour_strength,
                "times_of_day" => $times_of_day,
                "describe_favourite" => $describe_favourite,
                "salad_dressings" => $salad_dressings,
                "flavour_notes" => $flavour_notes,
                "per_day" => $per_day,
                "suggestion_1" => $suggestion_1,
                "suggestion_1_url" => $suggestion_1_url,
                "suggestion_1_title" => $suggestion_1_title,
                "suggestion_1_image" => $suggestion_1_image,
                "suggestion_2" => $suggestion_2,
                "suggestion_2_url" => $suggestion_2_url,
                "suggestion_2_title" => $suggestion_2_title,
                "suggestion_2_image" => $suggestion_2_image
              )
            )
        );

        $res = subscribeToKlaviyoList($data);

        if($res == 'ok') {
          echo json_encode(array( 'msg' => 'Subscribed' ));
        } elseif($res == 'existing') {
          
          $res1 = unsubscribeUserFromKlaviyoList($data, $email);
          if($res1 == 'deleted') {

            $res2 = subscribeToKlaviyoList($data);
            if($res2 == 'ok') {
              echo json_encode(array( 'msg_1' => 'Updated' ));
            } else {
              echo json_encode(array( 'msg_2' => $res2 ));
            }

          } elseif($res1 == 'error deleting') {
            echo json_encode(array( 'msg_3' => $res1 ));
          } else {
            echo json_encode(array( 'msg_4' => $res1 ));
          }

        } elseif($res == 'error') {
          echo json_encode(array( 'msg_5' => $res ));
        } else {
          echo json_encode(array( 'msg_6' => $res ));
        }

    } else {
      echo json_encode(array( 'msg_7' => 'error: No input email!' ));
    }
  } else {
    echo json_encode(array( 'msg_8' => 'error: No $_POST!' ));
  }







  function subscribeToKlaviyoList($data) {
    $retVal = 'initial';

    $data_string = json_encode($data);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://a.klaviyo.com/api/v2/list/T6TKKU/subscribe');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Access-Control-Allow-Origin: *';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);        
    $result_obj = json_decode($result);

    if (curl_errno($ch)) {
      $retVal = 'error';
    } elseif(count($result_obj) == 0) {
      $retVal = 'existing';
    } else {
      $retVal = 'ok';
    }

    curl_close($ch);

    return $retVal;
  }

  function unsubscribeUserFromKlaviyoList($data, $email) {
    $retVal = 'initial';
    $data_string = json_encode($data);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://a.klaviyo.com/api/v2/list/T6TKKU/subscribe?api_key=pk_6a935db3a6acd08e51e3f93e0bb85bd926&emails=' . $email);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

    $result = curl_exec($ch);
    $result_obj = json_decode($result);

    if (curl_errno($ch)) { 
      $retVal = 'error deleting';
    } else {
      $retVal = 'deleted';
    }

    curl_close($ch);

    return $retVal;
  }

?>