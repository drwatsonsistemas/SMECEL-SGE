<?php
   $apiKey = "";
   $data = array("model" => "text-davinci-002", "prompt" => "O que é OpenAI?");
   $data_string = json_encode($data);
   $ch = curl_init('https://api.openai.com/v1/engines/davinci/jobs');
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
            'Authorization: Bearer ' . $apiKey
         ));
   $result = curl_exec($ch);
   $result = json_decode($result, true);
   $generated_text = $result['choices'][0]['text'];
   echo $generated_text;
?>