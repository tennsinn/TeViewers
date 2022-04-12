<?php

namespace TypechoPlugin\Viewers;

use Typecho\Db;
use Widget\Base\Contents;

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 文章筛选组件
 *
 * @package Viewers
 * @author 息E-敛
 * @link http://tennsinn.com
 * @license GNU General Public License 3.0
 */
class Posts extends Contents
{
    /**
     * 执行函数
     *
     * @throws Db\Exception
     */
    public function execute()
    {
        $this->parameter->setDefault(['size' => $this->options->postsListSize, 'order' => 'hot']);

        $sql = $this->select()
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.created < ?', $this->options->time)
            ->where('table.contents.type = ?', 'post');
        if ('hot' == $this->parameter->order)
            $sql->order('table.contents.clicksNum', Db::SORT_DESC);
        else
            $sql->order('RAND()');
        $this->db->fetchAll($sql->limit($this->parameter->size), [$this, 'push']);
    }
}
