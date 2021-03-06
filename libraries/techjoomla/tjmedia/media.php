<?php
/**
 * @package     Techjoomla.Libraries
 * @subpackage  Tjmedia
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.folder');

/**
 * Media handler
 *
 * @package     Joomla.Libraries
 * @subpackage  Media
 * @since       1.5
 */

class TJMedia
{
	/**
	 * Function to generate Thumbnail
	 *
	 * @param   String  $name              Class name
	 * @param   String  $file_name         Video file name
	 * @param   String  $destwithFilename  Video file location
	 * @param   String  $ffmpeg_path       Ffmpeg installation path
	 * @param   String  $thumbnail_height  New thumbnail height
	 * @param   String  $thumbnail_width   New thumbnail width
	 * @param   String  $thumbnail_path    Thumbnail storage path
	 *
	 * @return  array
	 */
	public static function getInstance($name, $file_name, $destwithFilename, $ffmpeg_path, $thumbnail_height, $thumbnail_width, $thumbnail_path)
	{
		// If no name sent, set a default name as media
		if (empty($name))
		{
			$name = 'media';
		}

		// Path where ffmpeg is located
		$ffmpeg = $ffmpeg_path;

		// Path where video file is located
		$video_path  = $destwithFilename;

		// Thumbnail size
		$thumb_height = $thumbnail_height;
		$thumb_width  = $thumbnail_width;
		$thumbSize    = $thumb_width . 'x' . $thumb_height;

		// Path to store generated thumbnail
		$videoname  = explode('.', $file_name);
		$videoname  = $videoname[0];
		$filetype   = $videoname[1];
		$thumb_path = JPATH_SITE . $thumbnail_path . $videoname . '.jpg';

		// Default time to get the image
		$second = 1;
		$output = array();

		// Create folder to store thumbnails
		if (!JFolder::exists(JPATH_SITE . $thumbnail_path))
		{
			JFolder::create(JPATH_SITE . $thumbnail_path);
		}

		$cmd = "{$ffmpeg} -i {$video_path} -deinterlace -an -ss {$second} -t 00:00:01  -s {$thumbSize} -r 1 -y -vcodec mjpeg -f mjpeg {$thumb_path} 2>&1";

		exec($cmd, $output, $retval);

		if ($retval)
		{
			// JFactory::getApplication()->enqueueMessage('Error generating thumbnail', 'error');
		}
		elseif ($output)
		{
			return array('videoname' => $videoname, 'videopath' => $video_path, 'filetype' => $filetype);
		}

		return true;
	}
}
