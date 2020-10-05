<?php
/**
 * 读者数据统计展示插件
 * 
 * @package Viewers
 * @author 息E-敛
 * @version 0.3.0
 * @link http://tennsinn.com
 **/
 
 class Viewers_Plugin implements Typecho_Plugin_Interface
 {
	/* 激活插件方法 */
	public static function activate()
	{
		Typecho_Plugin::factory('Widget_Archive')->singleHandle = array('Viewers_Plugin', 'addHitsNum');
		Typecho_Plugin::factory('Widget_Archive')->handleInit = array('Viewers_Plugin', 'reloadSelect');

		$db = Typecho_Db::get();
		if (!array_key_exists('hitsNum', $db->fetchRow($db->select()->from('table.contents'))))
			$db->query('ALTER TABLE `'. $db->getPrefix() .'contents` ADD `hitsNum` INT(10) DEFAULT 0;');
	}
 
	/* 禁用插件方法 */
	public static function deactivate()
	{
	}

	/* 插件配置方法 */
	public static function config(Typecho_Widget_Helper_Form $form)
	{
	}

	/* 个人用户的配置方法 */
	public static function personalConfig(Typecho_Widget_Helper_Form $form)
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
		$db = Typecho_Db::get();
		$query = $db->select('author', 'COUNT(author) AS num', 'url', 'mail')
			->from('table.comments')
			->where('authorId = ?', '0')
			->where('type = ?', 'comment')
			->where('status = ?', 'approved')
			->group('author')
			->order('num', Typecho_Db::SORT_DESC);
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
			echo '<a href="'.$Viewer['url'].'" title="'.$Viewer['author'].'" rel="nofollow"><img src="'.Typecho_Common::gravatarUrl($Viewer['mail'], $size, Helper::options()->commentsAvatarRating, NULL, NULL).'"></a></div>';
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
	public static function addHitsNum($archive, $select)
	{
		$db = Typecho_Db::get();
		$db->query($db->update('table.contents')->expression('hitsNum', 'hitsNum + 1')->where('cid = ?', $archive->stack[0]['cid']));
	}

	/**
	 * 重载Select条件
	 * @return void
	 */
	public static function reloadSelect($archive, $select)
	{
		$select->select('*');
	}
}
?>
