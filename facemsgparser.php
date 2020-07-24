<?php 
$path = "./facebook/messages/inbox/";
$watchWords = ["acid","weed","smoke","cannabis","porn"];
$found = [];
$links = [];
$phones = [];
$postcodes = [];
if ($handle = opendir($path)) {
    while (false !== ($file = readdir($handle))) {
        if ('.' === $file) continue;
        if ('..' === $file) continue;
#	echo $file."\n";
	$messages = file_get_contents($path."".$file."/message_1.json");
	$json = json_decode($messages,true);
	foreach ($json['messages'] as $msgWrap) {
		$msg = $msgWrap['content'];
		//check for watch words
		if(0 < count(array_intersect(array_map('strtolower', explode(' ', $msg)), $watchWords))) {
			#echo $msg;
			$found[$file][] = $msg;
		}
		//check for links
		preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $msg, $match);
		if (!empty($match[0])) {
			foreach ($match[0] as $k => $link) {
				$links[$file][] = $link;
			}
		}

		#preg_match_all('/\b[0-9]{3}\s*-\s*[0-9]{3}\s*-\s*[0-9]{4}\b/',$msg,$matches);
		preg_match("/[0-9]{10}/", $msg, $matches);
		if (!empty($matches[0])) {
			if (is_array($matches[0])) {
                        foreach ($matches[0] as $k => $phone) {
                                $phones[$file][] = $phone;
			}
			}
			else {
				$phones[$file][] = $matches[0];
			}
		}
		$postcode = '';
		$pattern = "/((GIR 0AA)|((([A-PR-UWYZ][0-9][0-9]?)|(([A-PR-UWYZ][A-HK-Y][0-9][0-9]?)|(([A-PR-UWYZ][0-9][A-HJKSTUW])|([A-PR-UWYZ][A-HK-Y][0-9][ABEHMNPRVWXY])))) [0-9][ABD-HJLNP-UW-Z]{2}))/i";

		preg_match($pattern, $msg, $matches);
		if (isset($matches[0])) {
		$postcode = $matches[0];
		if ($postcode!='') {
			$postcodes[$file][] = $postcode;
		}
		}
	}
    }
    closedir($handle);
}
var_dump($found);
var_dump($links);

var_dump($phones);
var_dump($postcodes);
