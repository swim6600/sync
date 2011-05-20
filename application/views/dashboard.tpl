hello <?php echo $user["email"]; ?> <br />
<a href="/signin/logout">logout</a> <br />
<a href="/weibo/connect">Sina Weibo</a>
<?php
if($weibo["is_connected"]) {
?>
<a href="/weibo/disconnect">remove</a>
<?php
}
?>
<br />
<a href="/qq/connect">Tencent Weibo</a> <br />
