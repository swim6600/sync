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
<div class="title blue"><a href="<?php echo config::read('base.uri'); ?>"><?php echo $title; ?></a></div>
<div><?php echo $message; ?></div>
<div class="title blue"></div>
<div><?php echo date("Y-m-d H:i:s", $created); ?></div>
</div>
</blockquote>
</body>
</html>
