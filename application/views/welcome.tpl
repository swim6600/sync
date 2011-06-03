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
<div class="title orange">SyncBot 是什么？</div>
<blockquote>
<div>SyncBot 提供这样的服务：同步你的微博。经过一次设置之后，你只需要在你的其中一个微博发布消息，SyncBot会自动把这条消息同步到你关联的其他微博帐号中去。</div>
</blockquote>
<div class="title yellow">SyncBot 目前支持哪些微博?</div>
<blockquote>
<div>目前该服务还在测试阶段，仅支持 Twitter、新浪微博以及腾讯微博。更多的支持计划暂时不明。</div>
</blockquote>
<div class="title blue">SyncBot 是一个集成多种微博的“客户端”吗？</div>
<blockquote>
<div>不是。SyncBot并不是一个客户端，而是一个服务。你可以使用任何你喜欢的客户端发布你的微博。在一次帐号设置以后，更本不用再关心SyncBot。</div>
</blockquote>
<div class="title red">SyncBot 会把我的Timeline搞乱吗？</div>
<blockquote>
<div>不会。SyncBot会设置一个主帐号，所有的同步只是从这个主帐号读取最新的更新，然后发布到其他关联的微博。主帐号可以自由切换。</div>
</blockquote>
<div class="title purple"></div>
<div><a href="<?php echo config::read('base.uri'); ?>signup">注册</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo config::read('base.uri'); ?>signin">登陆</a></div>
</blockquote>
</div>
</body>
</html>
