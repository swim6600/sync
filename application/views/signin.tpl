<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
@import "/css/default.css";
</style>
</head>
<body>
<blockquote>
<div class="divBody">
<div class="title blue"><?php echo $title; ?></div>
<form action="/signin/auth" method="post">
<ul>
<li>email: <input type="text" name="email" class="input" /></li>
<li>password: <input type="password" name="password" class="input" /></li>
<li><input type="hidden" name="redirect" class="input" value="<?php echo $redirect;?>" /></li>
<li><input type="submit" /></li>
</ul>
</form>
</div>
</blockquote>
</body>
</html>
