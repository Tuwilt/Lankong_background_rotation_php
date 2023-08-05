<?php

function get_response($now_page_url) {

   $curl = curl_init();

   curl_setopt_array($curl, array(
      CURLOPT_URL => $now_page_url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
         'Authorization: Bearer yourToken',
         'Accept: application/json'
      ),
   ));

   $response = curl_exec($curl);

   curl_close($curl);

   global $now_page_url;
   $now_page_url = null;

   return $response;
}

function find_urls($array) {
   global $urls;
   global $now_page_url;
   // 此处填写图床的访问网址
   $needle = "your imagehost's url";

   foreach ($array as $key => $value) {
      if (is_array($value)) {
         find_urls($value);
      }
      elseif (is_string($value) && $key == "url" && strpos($value, $needle) !== false) {
         $urls[] = $value;
      }

      if ($key == "next_page_url" && $value !== null) {
         $now_page_url = $value;
      }
   }
 }

// 此处填写兰空图床中的接口URL，后面的参数修改album_id为希望使用的相册id即可
// 如果想要更精确的使用图床图片，请自行阅读兰空图床的接口文档
$now_page_url = "http://your imagehost's URL/images?page=1&album_id=0&permission=private";
$urls = array();

while ($now_page_url !== null) {
   $response = get_response($now_page_url);

   if ($response == false) {
      // 此处填写备用图片的url，防止图床挂了
      $urls[] = "your backup image's url";
      break;
   }

   $json = json_decode($response, true);
   find_urls($json);
}

header("Location:".$urls[array_rand($urls)]);

?>