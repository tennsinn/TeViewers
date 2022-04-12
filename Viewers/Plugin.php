<?php

namespace TypechoPlugin\Viewers;

use Typecho\Plugin\PluginInterface;
use Typecho\Db;
use Typecho\Widget;
use Typecho\Date;
use Typecho\Common;
use Typecho\Widget\Helper\Form;
use Utils\Helper;

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 读者数据统计展示插件
 * 
 * @package Viewers
 * @author 息E-敛
 * @version 0.5.3
 * @link http://tennsinn.com
 **/
 
 class Plugin implements PluginInterface
 {
	/* 激活插件方法 */
	public static function activate()
	{
		\Typecho\Plugin::factory('Widget_Archive')->singleHandle = array('Viewers_Plugin', 'addClicksNum');
		\Typecho\Plugin::factory('Widget_Archive')->handleInit = array('Viewers_Plugin', 'selectAll');

		$db = Db::get();
		if (!array_key_exists('clicksNum', $db->fetchRow($db->select()->from('table.contents'))))
			$db->query('ALTER TABLE `'. $db->getPrefix() .'contents` ADD `clicksNum` INT(10) DEFAULT 0;');
		if (!array_key_exists('clicked', $db->fetchRow($db->select()->from('table.contents'))))
			$db->query('ALTER TABLE `'. $db->getPrefix() .'contents` ADD `clicked` INT(10) DEFAULT 0;');
	}
 
	/* 禁用插件方法 */
	public static function deactivate()
	{
	}

	/* 插件配置方法 */
	public static function config(Form $form)
	{
	}

	/* 个人用户的配置方法 */
	public static function personalConfig(Form $form)
	{
	}

	/**
	 * 插件实现方法
	 * @param  string  $mode  显示模式
	 * @param  integer $limit 获取数量
	 * @param  integer $size  头像尺寸
	 * @return void
	 */
	public static function render($mode=NULL, $limit=NULL, $size=40)
	{
		$mode = $mode ? 'full' : 'brief';
		$db = Db::get();
		$query = $db->select('author', 'COUNT(author) AS num', 'url', 'mail')
			->from('table.comments')
			->where('authorId = ?', '0')
			->where('type = ?', 'comment')
			->where('status = ?', 'approved')
			->group('author, mail, url')
			->order('num', Db::SORT_DESC);
		if($limit)
			$query->limit($limit);
		$Viewers = $db->fetchAll($query);
		echo '<div id="Viewers" class="clearFix">';
		echo '<link rel="stylesheet" type="text/css" href="';
		Helper::options()->pluginUrl('/Viewers/Viewers.css');
		echo '">';
		foreach ($Viewers as $Viewer)
		{
			echo '<div class="viewers_'.$mode.'_viewer">';
			echo '<div class="viewers_'.$mode.'_avatar">';
			echo '<a href="'.$Viewer['url'].'" title="'.$Viewer['author'].'" rel="nofollow"><img src="'.Common::gravatarUrl($Viewer['mail'], $size, Helper::options()->commentsAvatarRating).'"></a></div>';
			if($mode=='full')
				echo '<span class="viewers_full_author">'.$Viewer['author'].'</span>';
			echo '<span class="viewers_'.$mode.'_num">'.$Viewer['num'].'</span>';
			echo '</div>';
		}
		echo '</div><div class="clearFix"></div>';
	}

	/**
	 * 增加点击计数
	 * @return void
	 */
	public static function addClicksNum($archive, $select)
	{
		Widget::widget('Widget_User')->to($user);
		if(!$user->hasLogin() || $user->uid != $archive->authorId)
		{
			$db = Db::get();
			$update = $db->update('table.contents')->where('cid = ?', $archive->cid);
			$update->expression('clicksNum', 'clicksNum + 1');
			$update->expression('clicked', Date::gmtTime());
			$db->query($update);
		}
	}

	/**
	 * 重载Select条件
	 * @return void
	 */
	public static function selectAll($archive, $select)
	{
		$select->select('*');
	}
}
?>
