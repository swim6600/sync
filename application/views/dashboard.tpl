hello <?php echo $user["email"]; ?> <br />
<a href="/signin/logout">logout</a> <br />
<?php
if($weibo) {
?>
<a href="/weibo/disconnect">remove Weibo</a>
<?php
if($weibo->is_main_network) {
?>
| Main network
<?php
}else {
?>
<a href="/weibo/main_network">Set as Main network</a>
<?php
}
}else {
?>
<a href="/weibo/connect"><img src="/images/weibo_240.png" /></a>
<?php
}
?>
<br />
<?php
if($tencent) {
?>
<a href="/tencent/disconnect">remove Tencent</a>
<?php
if($tencent->is_main_network) {
?>
| Main network
<?php
}else {
?>
| <a href="/tencent/main_network">Set as Main network</a>
<?php
}
}else {
?>
<a href="/tencent/connect"><img src="/images/tencent_24.png" /></a>
<?php
}
?>
<br />
<?php
if($twitter) {
?>
<a href="/twitter/disconnect">remove Twitter</a>
<?php
if($twitter->is_main_network) {
?>
| Main network
<?php
}else {
?>
| <a href="/twitter/main_network">Set as Main network</a>
<?php
}
}else {
?>
<a href="/twitter/connect"><img src="/images/sign-in-with-twitter-d.png"></a>
<?php
}
?>
<br />
