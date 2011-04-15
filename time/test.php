<?php

include_once "api/swift/swift_required.php";
//Create the message
$message = Swift_Message::newInstance()

//Give the message a subject
->setSubject('Your subject')

//Set the From address with an associative array
->setFrom(array('breadandroses@gmail.com' => 'Bread and Roses'))

//Set the To addresses with an associative array
->setTo(array('maxolasersquad@gmail.com'))

//Give it a body
->setBody('Here is the message itself')

//And optionally an alternative body
->addPart('<q>Here is the message itself</q>', 'text/html')

//Optionally add any attachments
//->attach(Swift_Attachment::fromPath('my-document.pdf'))
;

$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
  ->setUsername('breadandrosesfoodcoop@gmail.com')
  ->setPassword('tallahassee')
  ;

$mailer = Swift_Mailer::newInstance($transport);

echo $mailer->send($message);
?>
