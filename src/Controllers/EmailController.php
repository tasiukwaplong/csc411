<?php
/**
 * @author tasiukwaplong
 */
class EmailController {
    public $secret_key = 'tk_test_5706c144312501134be78eb5dcdf30989c387c03';

    public function sendMail($mailAddress, $subject, $body){
        $data = [
            'sk'=>$this->secret_key,
            'email'=>$mailAddress,
            'subject'=>$subject,
            'body'=>$body
        ];
        //make api call to send email
          $curl = curl_init();
          curl_setopt_array($curl, [
            CURLOPT_URL => "https://script.google.com/macros/s/AKfycbwRuWSNXU4Em7jYPZ41Z3V0wtr772KgsUgwYbKVz7gFOxg1IZo/exec",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
              "content-type: application/json"
            ]
          ]);
          $response = curl_exec($curl);
          $err = curl_error($curl);
          
          curl_close($curl);
          return ($err) ? null : $response;
      
        }
}
