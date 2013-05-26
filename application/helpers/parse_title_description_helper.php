<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('parse_title_description_helper'))
{
    function parse_title_description_helper($url)
    {	
    	try{
	    	$obj = array('title' => '', 'description'=>'');	
			$html = file_get_contents_curl($url);
			$doc = new DOMDocument();
			@$doc->loadHTML($html);
			$nodes = $doc->getElementsByTagName('title');
			//get and display what you need:
			if(isset($nodes->item(0)->nodeValue)){
				$title = $nodes->item(0)->nodeValue;
			}else{
				throw new Exception("ahh real monsters, just get into exception but throw empty values.");
			}
			$obj['title'] = $title;
			$description = '';

			$metas = $doc->getElementsByTagName('meta');

			for ($i = 0; $i < $metas->length; $i++)
			{
			    $meta = $metas->item($i);
			    if($meta->getAttribute('name') == 'description')
			        $description = $meta->getAttribute('content');		    
			}

			$obj['description'] = $description;

	    	return $obj;
	    }catch(Exception $e){
	    	return array('title' => '', 'description'=>'');
	    }
    }
}

if ( ! function_exists('file_get_contents_curl'))
{
	function file_get_contents_curl($url)
	{
	    $ch = curl_init();

	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

	    $data = curl_exec($ch);
	    curl_close($ch);

	    return $data;
	}
}