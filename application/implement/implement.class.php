<?php
class implement extends object {
    public $i = 0;
    public $html;
    public $links;
    public $contents;

    public function __construct() {
    }

    protected function reset() {
        $this->html = '';
        $this->links = array();
        $this->contents = array();
    }

    protected function save($data) {
        $this->log = $this->getInstance('logManager');
        $db = $this->getInstance('dbManager');
        $created = date("Ym", $data->created);
        $checkTable = $db->execute("select id from data_{$created} limit 0, 1");
        if($checkTable === false) {
            $db->execute(sprintf(config::read('createTableScript'), $created));
        }
        // check if the thread already exists
        $ret = $db->getArray("select id from data_{$created} where bbs_id = '{$data->id}' and thread_id = '{$data->threadId}'");
        if(empty($ret)) {
            // insert a new one
            $ret = $db->getArray("select id from cat where name ='{$data->category}'");
            if(empty($ret)) {
                $db->execute("insert into cat(cid, name) values (0, '{$data->category}')");
                $catId = $db->Insert_ID();
            }else {
                $catId = $ret[0][0];
            }
            $db->execute(
                sprintf(
                    config::read('insertDataScript'),
                    $created,
                    $data->id,
                    $data->subject,
                    $data->author,
                    $data->reply,
                    $data->view,
                    $data->created,
                    $data->updated,
                    $catId,
                    $data->forumId,
                    $data->threadId,
                    $data->link,
                    $data->authorLink
                )
            );
            $this->log->console->log("insert ok");
        }else {
            // update updated and reply/view
            $id = $ret[0][0];
            $db->execute(
                sprintf(
                    config::read('updateDataScript'),
                    $created,
                    $data->subject,
                    $data->reply,
                    $data->view,
                    $data->updated,
                    $id
                )
            );
            $this->log->console->log("update ok");
        }
    }

    protected function fixLink($server, $link) {
        if(substr($link, 0, 7) == 'http://') {
            return $link;
        }else if(substr($link, 0, 1) == '/') {
            return $server . $link;
        }else {
            return $server . '/' . $link;
        }
    }

    protected function fixLinks($server, $format, $ignore = array()) {
        foreach($this->links as $key => $link) {
            $parse = parse_url($link['href']);
            $link['href'] = $this->fixLink($server, $link['href']);
            if(!preg_match($format, $link['href'])) {
                unset($this->links[$key]);
                continue;
            }
            if(in_array($link['href'], $ignore)) {
                unset($this->links[$key]);
                continue;
            }
            $this->links[$key]['href'] = $link['href'];
        }
    }

    protected function findNodesByTag($tidy, $tag_name, $class_name = '') {
        if(is_object($tidy)) {
            if(isset($tidy->name)) {
                if($tidy->name == strtolower($tag_name)) {
                    if($class_name) {
                        if(isset($tidy->attribute['class']) && $tidy->attribute['class'] == strtolower($class_name)) {
                            $this->contents[] = $tidy;
                            return true;
                        }else {
                            if($tidy->hasChildren()) {
                                foreach($tidy->child as $tidy_child) {
                                    $this->findNodesByTag($tidy_child, $tag_name, $class_name);
                                }
                            }else {
                                return false;
                            }
                        }
                    }else {
                        $this->contents[] = $tidy;
                        return true;
                    }
                }else {
                    if($tidy->hasChildren()) {
                        foreach($tidy->child as $tidy_child) {
                            $this->findNodesByTag($tidy_child, $tag_name, $class_name);
                        }
                    }else {
                        return false;
                    }
                }
            }else {
                if($tidy->hasChildren()) {
                    foreach($tidy->child as $tidy_child) {
                        $this->findNodesByTag($tidy_child, $tag_name, $class_name);
                    }
                }else {
                    return false;
                }
            }
        }else {
            return false;
        }
    }

    protected function findNodeByClass($tidy, $class_name, $class_number = 0) {
        if(is_object($tidy)) {
            if(isset($tidy->attribute['class'])) {
                if($tidy->attribute['class'] == $class_name) {
                    if($this->i == $class_number) {
                        $this->i = 0;
                        return $tidy;
                    }else {
                        $this->i ++;
                        if($tidy->hasChildren()) {
                            foreach($tidy->child as $tidy_child) {
                                $tidy_ret = $this->findNodeByClass($tidy_child, $class_name, $class_number);
                                if($tidy_ret) {
                                    return $tidy_ret;
                                }
                            }
                        }else {
                            return false;
                        }
                    }
                }else {
                    if($tidy->hasChildren()) {
                        foreach($tidy->child as $tidy_child) {
                            $tidy_ret = $this->findNodeByClass($tidy_child, $class_name, $class_number);
                            if($tidy_ret) {
                                return $tidy_ret;
                            }
                        }
                    }else {
                        return false;
                    }
                }
            }else {
                if($tidy->hasChildren()) {
                    foreach($tidy->child as $tidy_child) {
                        $tidy_ret = $this->findNodeByClass($tidy_child, $class_name, $class_number);
                        if($tidy_ret) {
                            return $tidy_ret;
                        }
                    }
                }else {
                    return false;
                }
            }
        }else {
            return false;
        }
    }

    protected function findNodeById($tidy, $class_name, $class_number = 0) {
        if(is_object($tidy)) {
            /**
             * 是对象,检查对象的 attribute 属性, 如果不空, 检查 class 属性
             */
            if(isset($tidy->attribute['id'])) {
                if($tidy->attribute['id'] == $class_name) {
                    if($this->i == $class_number) {
                        $this->i = 0;
                        return $tidy;
                    }else {
                        /**
                         * 找到节点, 但是非指定, 继续找子节点
                         */
                        $this->i ++;
                        if($tidy->hasChildren()) {
                            /**
                             * 找到子节点,递归查找
                             */
                            foreach($tidy->child as $tidy_child) {
                                $tidy_ret = $this->findNodeById($tidy_child, $class_name, $class_number);
                                if($tidy_ret) {
                                    return $tidy_ret;
                                }
                            }
                        }else {
                            /**
                             * 没有子节点, 没有找到class
                             */
                            return false;
                        }
                    }
                }else {
                    /**
                     * 没有找到, 是否有子节点
                     */
                    if($tidy->hasChildren()) {
                        /**
                         * 找到子节点,递归查找
                         */
                        foreach($tidy->child as $tidy_child) {
                            $tidy_ret = $this->findNodeById($tidy_child, $class_name, $class_number);
                            if($tidy_ret) {
                                return $tidy_ret;
                            }
                        }
                    }else {
                        /**
                         * 没有子节点, 没有找到class
                         */
                        return false;
                    }
                }
            }else {
                /**
                 * 没有属性, 是否有子节点
                 */

                if($tidy->hasChildren()) {
                    /**
                     * 找到子节点,递归查找
                     */
                    foreach($tidy->child as $tidy_child) {
                        $tidy_ret = $this->findNodeById($tidy_child, $class_name, $class_number);
                        if($tidy_ret) {
                            return $tidy_ret;
                        }
                    }
                }else {
                    /**
                     * 没有子节点, 没有找到class
                     */
                    return false;
                }
            }
        }else {
            return false;
        }
    }

    protected function findNodeByTag($tidy, $tag_name, $tag_number = 0) {
        if(is_object($tidy)) {
            if(isset($tidy->name)) {
                if($tidy->name == strtolower($tag_name)) {
                    if($this->i == $tag_number) {
                        $this->i = 0;
                        return $tidy;
                    }else {
                        $this->i ++;
                        if($tidy->hasChildren()) {
                            foreach($tidy->child as $tidy_child) {
                                $tidy_ret = $this->findNodeByTag($tidy_child, $tag_name, $tag_number);
                                if($tidy_ret) {
                                    return $tidy_ret;
                                }
                            }
                        }else {
                            return false;
                        }
                    }
                }else {
                    if($tidy->hasChildren()) {
                        foreach($tidy->child as $tidy_child) {
                            $tidy_ret = $this->findNodeByTag($tidy_child, $tag_name, $tag_number);
                            if($tidy_ret) {
                                return $tidy_ret;
                            }
                        }
                    }else {
                        return false;
                    }
                }
            }else {
                if($tidy->hasChildren()) {
                    foreach($tidy->child as $tidy_child) {
                        $tidy_ret = $this->findNodeByTag($tidy_child, $tag_name, $tag_number);
                        if($tidy_ret) {
                            return $tidy_ret;
                        }
                    }
                }else {
                    return false;
                }
            }
        }else {
            return false;
        }
    }

    protected function findNodeContentByClass($tidy, $class_name) {
        if(is_object($tidy)) {
                if(isset($tidy->attribute['class'])) {
                    $this->html .= $tidy->value;
                }else {
                    if($tidy->hasChildren()) {
                        foreach($tidy->child as $tidy_child) {
                            $this->findNodeContentByClass($tidy_child, $class_name);
                        }
                    }else {
                        return false;
                    }
                }
        }else {
            return false;
        }
    }

    protected function findNodeContent($tidy, $ignore_class_name = array()) {
        if(is_object($tidy)) {
            if(isset($tidy->name)) {
                if($ignore_class_name) {
                    if(isset($tidy->attribute['class']) && in_array($tidy->attribute['class'], $ignore_class_name)) {
                        return false;
                    }
                }
                if($tidy->istext() && $txt = trim($tidy->value)) {
                    $this->html .= $txt . "\n";
                }elseif($tidy->name == 'img') {
                    return false;
                    //$this->html .= "<img src=\"" . $tidy->attribute['src'] . "\" />" . "\n";
                }elseif($tidy->name == 'script') {
                    return false;
                }else {
                    if($tidy->hasChildren()) {
                        foreach($tidy->child as $tidy_child) {
                            $this->findNodeContent($tidy_child, $ignore_class_name);
                        }
                    }else {
                        return false;
                    }
                }
            }
        }else {
            return false;
        }
    }

    protected function findNodeText($tidy, $dump = false) {
        if(is_object($tidy)) {
            if($tidy->istext()) {
                return trim($tidy->value);
            }else {
                if($tidy->hasChildren()) {
                    foreach($tidy->child as $tidy_child) {
                        $tidy_ret = $this->findNodeText($tidy_child, $dump);
                        if($tidy_ret) {
                            return $tidy_ret;
                        }
                    }
                }else {
                    return false;
                }
            }
        }else {
            return false;
        }
    }

    protected function findNodeLinks($tidy) {
        if(is_object($tidy)) {
            if(isset($tidy->name)) {
                if($tidy->name == 'a') {
                    if(isset($tidy->attribute['href'])) {
                        $key = count($this->links);
                        $this->links[$key]['title'] = $this->findNodeText($tidy);
                        $this->links[$key]['href'] = trim($tidy->attribute['href']);
                    }else {
                        if($tidy->hasChildren()) {
                            foreach($tidy->child as $tidy_child) {
                                $this->findNodeLinks($tidy_child);
                            }
                        }else {
                            return false;
                        }
                    }
                }else {
                    if($tidy->hasChildren()) {
                        foreach($tidy->child as $tidy_child) {
                            $this->findNodeLinks($tidy_child);
                        }
                    }else {
                        return false;
                    }
                }
            }else {
                if($tidy->hasChildren()) {
                    foreach($tidy->child as $tidy_child) {
                        $this->findNodeLinks($tidy_child);
                    }
                }else {
                    return false;
                }
            }
        }else {
            return false;
        }
    }
    
    protected function findNodeLink($tidy) {
        if(is_object($tidy)) {
            if(isset($tidy->name)) {
                if($tidy->name == 'a') {
                    if(isset($tidy->attribute['href'])) {
                        $link = array();
                        $link['title'] = $this->findNodeText($tidy);
                        $link['href'] = trim($tidy->attribute['href']);
                        return $link;
                    }else {
                        if($tidy->hasChildren()) {
                            foreach($tidy->child as $tidy_child) {
                                $link = $this->findNodeLink($tidy_child);
                                if($link) {
                                    return $link;
                                }
                            }
                        }else {
                            return false;
                        }
                    }
                }else {
                    if($tidy->hasChildren()) {
                        foreach($tidy->child as $tidy_child) {
                            $link = $this->findNodeLink($tidy_child);
                            if($link) {
                                return $link;
                            }
                        }
                    }else {
                        return false;
                    }
                }
            }else {
                if($tidy->hasChildren()) {
                    foreach($tidy->child as $tidy_child) {
                        $link = $this->findNodeLink($tidy_child);
                        if($link) {
                            return $link;
                        }
                    }
                }else {
                    return false;
                }
            }
        }else {
            return false;
        }
    }
}
