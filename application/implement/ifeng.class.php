<?php
class ifeng extends implement {
    protected $http;
    private $server;
    private $entrance;
    private $forums;
    private $ignoreThreadId;
    private $forumLinkFormat;
    private $threadLinkFormat;
    public $results;
    
    public function __construct() {
    	exit;
        $this->results = array();
        //$this->http = $this->getInstance('http');
        $logManager = new logManager();
        $log = $logManager->getInstance();
        $dbManager = new dbManager();
        $db = $dbManager->getInstance();
        $rs = $db->Execute("show databases;");
        $results = adodb_getall($rs);
        exit;
        $this->tidy = $this->getInstance('tidyManager');
        $this->server = 'http://bbs.ifeng.com';
        $this->entrance = 'http://bbs.ifeng.com/channel/society.php';
        $this->forumLinkFormat = 'http://bbs.ifeng.com/forumdisplay.php?fid=%s&page=%s';
        $this->forumLinkFormatSimple = 'http://bbs.ifeng.com/forumdisplay.php?fid=%s';
        $this->threadLinkFormat = 'http://bbs.ifeng.com/viewthread.php?tid=%s&extra=page%3D%s';
        $this->getForums();
        $this->getThreads();
        $this->tmp($this->results);
    }

    private function getForums() {
        $pageHtml = $this->http->get($this->entrance);
        //$pageHtml = file_get_contents('forum');
        $this->tidy->parseString($pageHtml, $this->tidy->config, $this->tidy->charset);
        $tidyBody = $this->tidy->body();
        $tidyBian = $this->findNodeByClass($tidyBody, 'bian');
        $this->findNodeLinks($tidyBian);
        $this->links[] = '/forumdisplay.php?fid=1&page=2';
        $this->fixLinks(
            $this->server,
            '/http:\/\/bbs.ifeng.com\/forumdisplay.php\?fid=[0-9]+/',
            array(
                sprintf($this->forumLinkFormatSimple, '352'),
                sprintf($this->forumLinkFormatSimple, '287'),
                sprintf($this->forumLinkFormatSimple, '323')
            )
        );
        $this->forums = $this->links;
    }

    private function getThreads() {
        foreach($this->forums as $forum) {
            preg_match("/fid=[0-9]+/", $forum['href'], $output);
            list($tmp, $fid) = explode('=', $output[0]);

            $url = sprintf($this->forumLinkFormatSimple, $fid);
            $listHtml = $this->http->get($url);
            if($listHtml === false) {
                $this->log->console->log("ok, ifeng-{$fid}, page is not a valid page");
                continue;
            }
            //file_put_contents('thread', $listHtml);
            //$listHtml = file_get_contents('thread');
            $this->tidy = $this->getInstance('tidyManager', true);
            $this->tidy->parseString($listHtml, $this->tidy->config, $this->tidy->charset);
            $tidyBody = $this->tidy->body();
            $tidyList = $this->findNodeByClass($tidyBody, 'titleList');
            $this->findNodesByTag($tidyList, 'tr');
            $contents = $this->contents;
            foreach($contents as $tidyContent) {
                if($tidyContent->child[0]->attribute['class'] == 'title01') {
                    continue;
                }
                $tidyTmp = $this->findNodeByTag($tidyContent, 'td', 1);
                $subject = $this->findNodeLink($tidyTmp);

                preg_match("/tid=[0-9]+/", $subject['href'], $output);
                list($tmp, $tid) = explode('=', $output[0]);

                //if(!empty($this->ignoreThreadId) && $this->ignoreThreadId > $tid) {
                //    $this->log->console->log("{$tid} is dead");
                //    continue;
                //}

                $link = $this->fixLink($this->server, $subject['href']);
                $pageHtml = $this->http->get($link);
                if($pageHtml === false) {
                    continue;
                }
                $this->tidy = $this->getInstance('tidyManager', true);
                $this->tidy->parseString($pageHtml, $this->tidy->config, $this->tidy->charset);
                $tidyTmp = $this->tidy->body();
                $tidyTmp = $this->findNodeByClass($tidyTmp, 'time');
                if($tidyTmp === false) {
                    continue;
                }
                $tidyTmp = $this->findNodeByTag($tidyTmp, 'h1');
                $created = $this->findNodeText($tidyTmp);
                if($created === false) {
                    continue;
                }
                preg_match("/[0-9 -:]+/", $created, $output);
                $created = strtotime(trim($output[0]));

                //$now = time();
                //$aliveDays = ceil(($now - $created) / (3600 * 24));
                //if($aliveDays > config::read('max_alive_days')) {
                //    // ignore this
                //    if(empty($this->ignoreThreadId) or $this->ignoreThreadId < $tid) {
                //        $this->ignoreThreadId = $tid;
                //    }
                //    $this->log->console->log("{$tid} is confirmed dead, it is the dead line now, created time: " . date('Y-m-d', $created));
                //    continue;
                //}

                $tidyTmp = $this->findNodeByTag($tidyContent, 'td', 2);
                $author = $this->findNodeLink($tidyTmp);
                $tidyTmp = $this->findNodeByTag($tidyContent, 'td', 3);
                $reply = $this->findNodeText($tidyTmp, true);
                $tidyTmp = $this->findNodeByTag($tidyContent, 'td', 4);
                $updated = $this->findNodeText($tidyTmp);

                list($r, $v) = explode('/', $reply);

                $data = new data;
                $data->id = 1;
                $data->author = $author['title'];
                $data->subject = $subject['title'];
                $data->reply = (int)$r;
                $data->view = (int)$v;
                $data->created = $created;
                $data->updated = strtotime($updated);
                $data->category = $forum['title'];
                $data->forumId = $fid;
                $data->threadId = $tid;
                $data->link = $link;
                $data->authorLink = $this->fixLink($this->server, $author['href']);
                $this->save($data);
                $this->log->console->log("ok, {$tid} is done");
            }
        }
    }
}
