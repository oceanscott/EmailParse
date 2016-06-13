<?php
/*
 * test_message_decoder.php
 *
 * @(#) $Header: /opt2/ena/metal/mimeparser/test_message_decoder.php,v 1.13 2012/04/11 09:28:19 mlemos Exp $
 *
 */
	

	require_once('rfc822_addresses.php');
	require_once('mime_parser.php');

	//assign eml file which you want to open
	$message_file=((IsSet($_SERVER['argv']) && count($_SERVER['argv'])>1) ? $_SERVER['argv'][1] : 'AMAZON_SES_SETUP_NOTIFICATION.eml');
	$mime=new mime_parser_class;
	
	/*
	 * Set to 0 for parsing a single message file
	 * Set to 1 for parsing multiple messages in a single file in the mbox format
	 */
	$mime->mbox = 0;
	
	/*
	 * Set to 0 for not decoding the message bodies
	 */
	$mime->decode_bodies = 1;

	/*
	 * Set to 0 to make syntax errors make the decoding fail
	 */
	$mime->ignore_syntax_errors = 1;

	/*
	 * Set to 0 to avoid keeping track of the lines of the message data
	 */
	$mime->track_lines = 1;

	/*
	 * Set to 1 to make message parts be saved with original file names
	 * when the SaveBody parameter is used.
	 */
	$mime->use_part_file_names = 0;

	/*
	 * Set this variable with entries that define MIME types not yet
	 * recognized by the Analyze class function.
	 */
	$mime->custom_mime_types = array(
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document'=>array(
			'Type' => 'ms-word',
			'Description' => 'Word processing document in Microsoft Office OpenXML format'
		)
	);

	$parameters=array(
		'File'=>$message_file,
		
		/* Read a message from a string instead of a file */
		/* 'Data'=>'My message data string',              */

		/* Save the message body parts to a directory     */
		/* 'SaveBody'=>'/tmp',                            */

		/* Do not retrieve or save message body parts     */
		'SkipBody'=>0,
	);

/*
 * The following lines are for testing purposes.
 * Remove these lines when adapting this example to real applications.
 */
	if(defined('__TEST'))
	{
		if(IsSet($__test_options['parameters']))
			$parameters=$__test_options['parameters'];
		if(IsSet($__test_options['mbox']))
			$mime->mbox=$__test_options['mbox'];
		if(IsSet($__test_options['decode_bodies']))
			$mime->decode_bodies=$__test_options['decode_bodies'];
		if(IsSet($__test_options['use_part_file_names']))
			$mime->use_part_file_names=$__test_options['use_part_file_names'];
	}

	if(!$mime->Decode($parameters, $decoded))
	{
		echo 'MIME message decoding error: '.$mime->error.' at position '.$mime->error_position;
		echo "<br>";
		if($mime->track_lines && $mime->GetPositionLine($mime->error_position, $line, $column))
			echo ' line '.$line.' column '.$column;
		echo "\n";
	}
	else
	{
		echo 'MIME message decoding successful.'."\n";
		echo "<br>";
		echo (count($decoded)==1 ? '1 message was found.' : count($decoded).' messages were found.'),"\n";
		echo "<br>";
		for($message = 0; $message < count($decoded); $message++)
		{
			echo 'Message ',($message+1),':',"\n";
			//echo "<br>";

			//var_dump($decoded[$message]);
			
			echo "<br>";
			/*echo "-------------------------------------------------------------";
			echo "<br>";
			print_r(array_keys($decoded['0']['ExtractedAddresses']['to:']));
			echo "<br>";
			*/
			if($mime->decode_bodies)
			{
				if($mime->Analyze($decoded[$message], $results)){
					//var_dump($results);
					/*
					echo "-------------------------------------------------------------";
					echo "<br>";
					
					echo "<br>";
					echo "-------------------------------------------------------------";
					echo "<br>";
					echo 'Type :'.$results["Type"];
					echo "<br>";
					*/
				}
				else
					echo 'MIME message analyse error: '.$mime->error."\n";
			}
		}
		ShowEmailInfo($decoded, $results);

		for($warning = 0, Reset($mime->warnings); $warning < count($mime->warnings); Next($mime->warnings), $warning++)
		{
			$w = Key($mime->warnings);
			echo 'Warning: ', $mime->warnings[$w], ' at position ', $w;
			if($mime->track_lines && $mime->GetPositionLine($w, $line, $column)){
				echo ' line '.$line.' column '.$column;
			
			//echo "sssssssssssssssssssssssssssssssssssssssssssssss123131";
			echo "\n";}
		}
	}

	function ShowEmailInfo($decoded, $results)
	{
		echo 'Date :'.$decoded['0']['Headers']['date:'] . '<br>';
		echo 'From :'.$decoded['0']['Headers']['from:'] . '<br>';
		echo 'to :'	.$decoded['0']['Headers']['to:'] . '<br>';
		echo 'subject :'.$decoded['0']['Headers']['subject:'] . '<br>';
		echo 'Body :'.$decoded['0']['Body']. '<br>';
		echo 'ExtractedAddresses :'.$decoded['0']['ExtractedAddresses']['to:']['0']["address"]. '<br>';
		echo 'ExtractedAddresses :'.$decoded['0']['ExtractedAddresses']['from:']['0']["address"]. '<br>';
		echo 'ExtractedAddresses :'.$decoded['0']['ExtractedAddresses']['from:']['0']["name"]. '<br>';

		
	
/*
array(6) { 
["Headers"]=> array(4) 
{
	["date:"]=> string(31) "Tue, 31 May 2016 10:46:23 +0000" 
	["to:"]=> string(21) "recipient@example.com" 
	["from:"]=> string(45) "Amazon Web Services " 
	["subject:"]=> string(29) "Amazon SES Setup Notification" 
}
["Parts"]=> array(0) 
{
}
["Position"]=> int(0) 
["BodyPart"]=> int(1) 
["BodyLength"]=> int(484) 
["ExtractedAddresses"]=> array(2)
{ExtractedAddresses
	["to:"]=> array(1)
	{
		[0]=> array(1)
		{
			["address"]=> string(21) "recipient@example.com"
		}
	}
	["from:"]=> array(1)
	{
		[0]=> array(2)
		{
			["address"]=> string(23) "no-reply-aws@amazon.com" ["name"]=> string(19) "Amazon Web Services"
		}
	}
} }


array(6) { ["Headers"]=> array(9) 
{ 
	["from "]=> string(1) "-" ["message-id:"]=> string(15) "" ["date:"]=> string(31) "Sat, 01 Jan 2000 00:00:00 -0000" ["from:"]=> string(13) "test@test.com" ["mime-version:"]=> string(3) "1.0" ["to:"]=> string(13) "test@test.com" ["subject:"]=> string(4) "test" ["content-type:"]=> string(30) "text/plain; charset=ISO-8859-1" ["content-transfer-encoding:"]=> string(4) "7bit" 
} 
["Parts"]=> array(0) 
{ 
}
["Position"]=> int(0) ["BodyPart"]=> int(1) ["BodyLength"]=> int(373) ["ExtractedAddresses"]=> array(2)
{ 
 	["from:"]=> array(1) 
 	{ [0]=> array(1)
  		{ 
  			["address"]=> string(13) "test@test.com" 
  		}
   	} ["to:"]=> array(1) 
   	{ [0]=> array(1)
   		{ 
   			["address"]=> string(13) "test@test.com" 
   		}
   	}
}
 } ------


["Subject"]
["Subject"]=> string(79) "Testing Manuel Lemos' MIME E-mail composing and sending PHP class: HTML message"
 ["Date"]=> string(31) "Sat, 30 Apr 2005 19:28:29 -0300" 
 ["From"]=> array(1) { [0]=> array(2) { ["address"]=> string(14) "mlemos@acm.org" ["name"]=> string(6) "mlemos" } }
 ["To"]=> array(1) { [0]=> array(2) { ["address"]=> string(18) "mlemos@linux.local" ["name"]=> string(12) "Manuel Lemos" } } 
 ["Return-path"]=> array(1) { [0]=> array(1) { ["address"]=> string(14) "mlemos@acm.org" } } 

		array(13) { ["Alternative"]=> array(1) { [0]=> array(4) { ["Type"]=> string(4) "text" ["Description"]=> string(12) "Text message" ["Encoding"]=> string(10) "iso-8859-1" ["DataLength"]=> int(91) } } ["Related"]=> array(2) { [0]=> array(7) { ["Type"]=> string(5) "image" ["SubType"]=> string(3) "gif" ["Description"]=> string(28) "Image file in the GIF format" ["DataLength"]=> int(1195) ["FileName"]=> string(8) "logo.gif" ["FileDisposition"]=> string(6) "inline" ["ContentID"]=> string(36) "ae0357e57f04b8347f7621662cb63855.gif" } [1]=> array(7) { ["Type"]=> string(5) "image" ["SubType"]=> string(3) "gif" ["Description"]=> string(28) "Image file in the GIF format" ["DataLength"]=> int(3265) ["FileName"]=> string(14) "background.gif" ["FileDisposition"]=> string(6) "inline" ["ContentID"]=> string(36) "4c837ed463ad29c820668e835a270e8a.gif" } } ["Attachments"]=> array(1) { [0]=> array(5) { ["Type"]=> string(4) "text" ["Description"]=> string(12) "Text message" ["DataLength"]=> int(64) ["FileName"]=> string(14) "attachment.txt" ["FileDisposition"]=> string(10) "attachment" } } 
		 ["Reply-to"]=> array(1) { [0]=> array(2) { ["address"]=> string(14) "mlemos@acm.org" ["name"]=> string(6) "mlemos" } } } 
		 */
	}
?>