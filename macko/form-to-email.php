<?php
if($_POST && isset($_FILES['my_file']))
{

    $from_email         = 'message@agmgroup.rs'; //from mail, it is mandatory with some hosts
    $recipient_email    = "nenadolazarevic@gmail.com"; //marko.milojevic@agmgroup.rs, goran.arbutina@agmgroup.rs, a.milosavljevic@agmgroup.rs, office@agmgroup.rs - recipient email (most cases it is your personal email)
   
    //Capture POST data from HTML form and Sanitize them,
    $first_name     = filter_var($_POST["first_name"], FILTER_SANITIZE_STRING); //first name
	$last_name     = filter_var($_POST["last_name"], FILTER_SANITIZE_STRING); //last name
    $reply_to_email = filter_var($_POST["sender_email"], FILTER_SANITIZE_STRING); //sender email used in "reply-to" header
    $subject        = "New message";
	$phone = filter_var($_POST["phone"], FILTER_SANITIZE_STRING);
    $message        = filter_var($_POST["message"], FILTER_SANITIZE_STRING); //message
	$email_body     = "You have received a new message.\n Sender: $first_name $last_name\n Phone: $phone\n Message:\n $message";
   
    /* //don't forget to validate empty fields
    if(strlen($first_name)<1){
        die('Name is too short or empty!');
    }
    */
   
    //Get uploaded file data
    $file_tmp_name    = $_FILES['my_file']['tmp_name'];
    $file_name        = $_FILES['my_file']['name'];
    $file_size        = $_FILES['my_file']['size'];
    $file_type        = $_FILES['my_file']['type'];
    $file_error       = $_FILES['my_file']['error'];

    if($file_error > 0)
    {
        header('Location: error.html');
    }
    //read from the uploaded file & base64_encode content for the mail
    $handle = fopen($file_tmp_name, "r");
    $content = fread($handle, $file_size);
    fclose($handle);
    $encoded_content = chunk_split(base64_encode($content));

        $boundary = md5("sanwebe");
        //header
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From:".$from_email."\r\n";
        $headers .= "Reply-To: ".$reply_to_email."" . "\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary = $boundary\r\n\r\n";
       
        //plain text
        $body = "--$boundary\r\n";
        $body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($email_body));
       
        //attachment
        $body .= "--$boundary\r\n";
        $body .="Content-Type: $file_type; name=".$file_name."\r\n";
        $body .="Content-Disposition: attachment; filename=".$file_name."\r\n";
        $body .="Content-Transfer-Encoding: base64\r\n";
        $body .="X-Attachment-Id: ".rand(1000,99999)."\r\n\r\n";
        $body .= $encoded_content;
   
    $sentMail = @mail($recipient_email, $subject, $body, $headers);
    if($sentMail) //output success or failure messages
    {      
        header('Location: thank-you.html');
    }else{
        die('Could not send mail! Please check your PHP mail configuration.');  
    }

}
   
?> 