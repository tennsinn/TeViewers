<?php
/**
 * 读者墙
 * 
 * @package Viewers
 * @author 息E-敛
 * @version 0.1.0
 * @link http://tennsinn.com
 **/
 
 class Viewers_Plugin implements Typecho_Plugin_Interface
 {
	/* 激活插件方法 */
	public static function activate()
	{
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
}
?>