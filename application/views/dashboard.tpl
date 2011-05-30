<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
@import "/css/default.css";
</style>
</head>
<body>
<div class="divIntro">
<blockquote>
<div class="title orange">帐号设置</div>
Hello <?php echo $user["email"]; ?> <br />
<blockquote>
<?php
if($weibo) {
?>
<a href="<?php echo config::read('base.uri'); ?>weibo/disconnect">取消新浪微博授权</a>
<?php
if($weibo->is_main_network) {
?>
| 主帐号
<?php
}else {
?>
<a href="<?php echo config::read('base.uri'); ?>weibo/main_network">设置为主帐号</a>
<?php
}
}else {
?>
<a href="<?php echo config::read('base.uri'); ?>weibo/connect"><img src="/images/weibo_240.png" /></a>
<?php
}
?>
<br />
<?php
if($tencent) {
?>
<a href="<?php echo config::read('base.uri'); ?>tencent/disconnect">取消腾讯微博授权</a>
<?php
if($tencent->is_main_network) {
?>
| 主帐号
<?php
}else {
?>
| <a href="<?php echo config::read('base.uri'); ?>tencent/main_network">设置为主帐号</a>
<?php
}
}else {
?>
<a href="<?php echo config::read('base.uri'); ?>tencent/connect"><img src="/images/tencent_24.png" /></a>
<?php
}
?>
<br />
<?php
if($twitter) {
?>
<a href="<?php echo config::read('base.uri'); ?>twitter/disconnect">取消Twitter授权</a>
<?php
if($twitter->is_main_network) {
?>
| 主帐号
<?php
}else {
?>
| <a href="<?php echo config::read('base.uri'); ?>twitter/main_network">设置为主帐号</a>
<?php
}
}else {
?>
<a href="<?php echo config::read('base.uri'); ?>twitter/connect"><img src="/images/sign-in-with-twitter-d.png"></a>
<?php
}
?>
<br />
</blockquote>
<a href="<?php echo config::read('base.uri'); ?>signin/logout">登出</a>
</blockquote>
</div>
</body>
</html>



