<?php
$bizink_bace = "https://bizinkcontent.com/wp-json/wp/v2/";
$bizinkcontent_client = array(
    'timeout' => 120,
    'httpversion' => '1.1',
    'headers' => array(
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer gvATLaalwnQoiZZsAcsHfqVMotLtgJCnWOGSTHvt'
    )
);
function bizinkblogs_getCategories(){
    global $bizink_bace,$bizinkcontent_client;
    $request = wp_remote_get($bizink_bace.'categories',$bizinkcontent_client);
    $body = json_decode(wp_remote_retrieve_body( $request ));
    return $body;
}

function bizinkblogs_getPosts($args = ['status' => 'publish','per_page' => 8]){
    global $bizink_bace,$bizinkcontent_client;
    $postUrl = add_query_arg($args,wp_slash($bizink_bace.'posts'));
    $request = wp_remote_get($postUrl ,$bizinkcontent_client);
    $body = json_decode(wp_remote_retrieve_body( $request ));
    return $body;
}