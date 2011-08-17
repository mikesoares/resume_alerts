<?php
	/*
		Resumé Alerts
		
		Created by:	Michael A. Soares (mikesoares.com)
		Date:		August 17, 2011
		Version:	1.00
		
		Description:
		Sends you an e-mail alert every time your resumé is read
		
		Requirements:
		A resumé (PDF or HTML)
		Everything under 'configuration stuff'
		PHPWhois:	http://sourceforge.net/projects/phpwhois/
		
		Caveats:
		Code is pretty rough but it does the job. Be sure to check your spam
		folder the first time an alert comes through and mark the e-mail as
		'Not Spam'.
		
		License:
		Released under the WTFPL.
	*/

	// configuration stuff
	$path_whois = '/path/to/whois.main.php';
	$path_resume = '/path/to/resume.html';
	$file_resume = 'resume_filename';	// no extension (this should be 
										// different from the actual resume
										// filename in $path_resume)
	$ext_resume = 'html';				// html or pdf, depending on above
	$alert_email = 'your@email.com';	// your e-mail address	
	
	// don't edit past here unless you know what you're doing
	include($path_whois);

	$ip = $_SERVER['REMOTE_ADDR'];
	$host = gethostbyaddr($ip);
	$referer = $_SERVER['HTTP_REFERER'];
	$user = $_SERVER['HTTP_USER_AGENT'];

	$whois = new Whois();
	$whois_ip = $whois->Lookup($ip);

	if(	strpos($host, 'crawl') !== false || 
		strpos($host, 'spider') !== false || 
		strpos($host, 'bot') !== false || 
		strpos($user, 'crawl') !== false || 
		strpos($user, 'spider') !== false || 
		strpos($user, 'bot') !== false
	) {
        $dontsend = true;
	}

    if(!isset($dontsend)) {
        $body = "IP: $ip\nHostname: $host\nReferer: $referer\nUser Agent: $user\n\nIP Registered To: {$whois_ip['regrinfo']['owner']['organization']}\nAddress: {$whois_ip['regrinfo']['owner']['address']['street']}, {$whois_ip['regrinfo']['owner']['address']['city']}, {$whois_ip['regrinfo']['owner']['address']['state']}, {$whois_ip['regrinfo']['owner']['address']['pcode']}, {$whois_ip['regrinfo']['owner']['address']['country']}";

        $header = "From: ".$alert_email."\r\n";

        @mail($alert_email, 'Your resume was read!', $body, $header);
    }
	
	if($ext_resume == 'pdf') header("Content-Type: application/pdf");
	else header("Content-Type: text/html");
	header("Content-Length: ".filesize($path_resume));
	header("Content-Disposition: inline; filename=".$file_resume.".".$ext_resume);
	readfile($path_resume);

?>
