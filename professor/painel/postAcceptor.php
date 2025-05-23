<?php
  /***************************************************
   * Only these origins are allowed to upload images *
   ***************************************************/
  $accepted_origins = array("https://www.smecel.com.br", "http://www.smecel.com.br", "http://localhost", "http://10.0.0.103");

  /*********************************************
   * Change this line to set the upload folder *
   *********************************************/
  $imageFolder = "../../anexos_imagens/";

  reset ($_FILES);
  $temp = current($_FILES);
  
  
  
  
  if (is_uploaded_file($temp['tmp_name'])){
	  
	  
  $nome = $temp['name'];
  
  $extensao = pathinfo ( $nome, PATHINFO_EXTENSION );
  $extensao = strtolower ( $extensao );
  $novoNome = uniqid ( time () ) . '.' . $extensao;
	  
	  
    if (isset($_SERVER['HTTP_ORIGIN'])) {
      // same-origin requests won't set an origin. If the origin is set, it must be valid.
      if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
      } else {
        header("HTTP/1.1 403 Origin Denied");
        return;
      }
    }

    /*
      If your script needs to receive cookies, set images_upload_credentials : true in
      the configuration and enable the following two headers.
    */
    // header('Access-Control-Allow-Credentials: true');
    // header('P3P: CP="There is no P3P policy."');

    // Sanitize input
    if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $novoNome)) {
        header("HTTP/1.1 400 Invalid file name.");
        return;
    }

    // Verify extension
    if (!in_array(strtolower(pathinfo($novoNome, PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
        header("HTTP/1.1 400 Invalid extension.");
        return;
    }

    // Accept upload if there was no origin, or if it is an accepted origin
    //$filetowrite = $imageFolder . $temp['name'];
	$filetowrite = $imageFolder . $novoNome;
    move_uploaded_file($temp['tmp_name'], $filetowrite);

    // Respond to the successful upload with JSON.
    // Use a location key to specify the path to the saved image resource.
    // { location : '/your/uploaded/image/file'}
    echo json_encode(array('location' => $filetowrite));
  } else {
    // Notify editor that the upload failed
    header("HTTP/1.1 500 Server Error");
  }
?>