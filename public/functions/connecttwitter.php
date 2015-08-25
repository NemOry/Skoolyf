<?php 

  require_once("../../includes/initialize.php");

  $config = array();
  $config['appId'] = APP_ID;
  $config['secret'] = APP_SECRET;
  $Twitter = new Twitter($config);

  $twitter_user = $Twitter->api('/me','GET');

  if($session->is_logged_in())
  {
    $current_user = User::get_by_id($session->user_id);
    $another_user = User::get_by_oauthid($twitter_user['id']);

    if($another_user == false)
    {
      $current_user->oauth_uid = $twitter_user['id'];
      $current_user->oauth_provider = "TWITTER";
      $current_user->update();
      header("location: ../../account.php?another_user: ".$another_user);
    }
    else if($current_user->username == $another_user->username)
    {
      $current_user->oauth_uid = $twitter_user['id'];
      $current_user->oauth_provider = "TWITTER";
      $current_user->update();
      header("location: ../../account.php?current: ".$current_user->oauth_uid.", another: ".$another_user->oauth_uid);
    }
    else
    {
      header("location: ../../account.php?twittertaken=Twitter Username: ".$twitter_user['username']."<br/>Twitter ID: ".$twitter_user['id']);
    }
  }
  else
  {
  	header("location: ../../index.php");
  }

?>