<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('json_helper'))
{
    function json_helper($url)
    {		
		$ch = curl_init( $url );
		$options = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HTTPHEADER => array('Content-type: application/json'));
		curl_setopt_array( $ch, $options );
		$result =  curl_exec($ch);
		$obj = json_decode($result);

    	//return result list
    	return $obj;
    }
}