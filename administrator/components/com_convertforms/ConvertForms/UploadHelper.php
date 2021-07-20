<?php

/**
 * @package         Convert Forms
 * @version         2.7.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.path');

/**
 *  Upload Helper class mainly used by the FileUpload field
 */
class UploadHelper
{
	/**
	 * Upload file
	 *
	 * @param	array	$file			The request file as posted by form
	 * @param	string	$upload_folder	The upload folder where the file must be uploaded
	 * @param	bool	$randomize		If is set to true, the filename will get a random prefix
	 * @param	bool	$allow_unsafe	Allow the upload of unsafe files. See JFilterInput::isSafeFile() method.
	 *
	 * @return	mixed	String on success, Null on failure
	 */
	public static function upload($file, $upload_folder, $randomize = true, $allow_unsafe = false)
	{
		// Make sure we have a valid file array
		if (!isset($file['name']) || !isset($file['tmp_name']))
		{
			return;
		}

		// Sanitize filename
		$filename = \JFile::makeSafe($file['name']);

		// Replace spaces with underscore
		$filename = str_replace(' ', '_', $filename);

		// Add a random prefix to filename to prevent replacing existing files accidentally
		if ($randomize)
		{
			$filename = self::randomizeFilename($filename);
		}

		$destination_filename = \JPath::clean(JPATH_ROOT . '/' . $upload_folder . '/' . $filename);

        if (!\JFile::upload($file['tmp_name'], $destination_filename, false, $allow_unsafe))
        {
			return;
		}

		return $filename;
	}

	/**
	 * Add a random prefix to filename
	 *
	 * @param  string $filename
	 *
	 * @return string
	 */
	public static function randomizeFilename($filename)
	{
		$prefix = substr(str_shuffle(md5(time())), 0, 10);
        return $prefix . '_' . $filename;
	}

	/**
	 * Checks whether a filename type is in an allowed list
	 *
	 * @param	mixed	$allowed_types	Array or a comma separated list of allowed file types. Eg: .jpg, .png, .gif
	 * @param	string	$file_path		The filename path to check
	 *
	 * @return	bool
	 */
	public static function isInAllowedTypes($allowed_types, $file_path)
	{
		// If empty assume, all files types are accepted
		if (empty($allowed_types))
		{
			return true;
		}

		if (is_string($allowed_types))
		{
			$allowed_types = explode(',', $allowed_types);
		}

		// Remove null and empty properties
		$allowed_types = array_filter($allowed_types);

		if (!$file_extension = \JFile::getExt($file_path))
		{
			return false;
		}

		$file_extension = strtolower($file_extension);

		foreach ($allowed_types as $allowed_extension)
		{
			if (strpos(strtolower($allowed_extension), $file_extension) !== false)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Return absolute full URL of a path
	 *
	 * @param	string	$path
	 *
	 * @return	string
	 */
	public static function absURL($path)
	{
		$path = str_replace([JPATH_SITE, JPATH_ROOT, \JURI::root()], '', $path);
		$path = \JPath::clean($path);

		// Convert Windows Path to Unix
		$path = str_replace('\\','/',$path);

		$path = ltrim($path, '/');
		$path = \JURI::root() . $path;

		return $path;
	}

	/**
	 * Make sure the folder does exist and it's writable. If the folder doesn't exist, it will try to create it.
	 *
	 * @param  string $path
	 *
	 * @return bool
	 */
	public static function folderExistsAndWritable($path)
	{
		if (!\JFolder::exists($path))
		{
			if (!\JFolder::create($path))
			{
				return false;
			}

			// New folder created. Let's protect it.
			self::writeHtaccessFile($path);
			self::writeIndexHtmlFile($path);
		}

		// Make sure the folder is writable
		return @is_writable($path);
	}

	/**
	 * Checks if the path exists. If not creates the folders as well as subfolders.
	 * 
	 * @param   string  $patth
	 * 
	 * @return  bool
	 */
	public static function checkFolderPathAndCreate($path)
	{
		if (!\JFolder::exists($path))
		{
			$folders = explode('/', substr($path, 1));
			$path_so_far = '';
			foreach ($folders as $folder)
			{
				$path_so_far .= '/' . $folder;
				
				if (!\JFolder::exists($path_so_far))
				{
					if (!\JFolder::create($path_so_far))
					{
						return false;
					}

					// New folder created. Let's protect it.
					self::writeHtaccessFile($path_so_far);
					self::writeIndexHtmlFile($path_so_far);
				}
			}
		}

		// Make sure the folder is writable
		return @is_writable($path);
	}

	/**
	 * Add an .htaccess file to the folder in order to disable PHP engine entirely 
	 *
	 * @param  string $path	The path where to write the file
	 *
	 * @return void
	 */
	public static function writeHtaccessFile($path)
	{
		$content = '
			# Turn off all options we don\'t need.
			Options None
			Options +FollowSymLinks

			# Disable the PHP engine entirely.
			<IfModule mod_php5.c>
				php_flag engine off
			</IfModule>

			# Block direct PHP access
			<Files *.php>
				deny from all
			</Files>
		';

		\JFile::write($path . '/.htaccess', $content);
	}

	/**
	 * Creates an empty index.html file to prevent directory listing 
	 *
	 * @param  string $path	The path where to write the file
	 *
	 * @return void
	 */
	public static function writeIndexHtmlFile($path)
	{
		\JFile::write($path . '/index.html', '<!DOCTYPE html><title></title>');	
	}

	/**
	 * Strip .tmp extension from a filename
	 *
	 * @param  string $path
	 *
	 * @return string
	 */
	public static function removeTmpSuffix($path)
	{
		if (strpos($path, '.tmp') === false)
		{
			return $path;
		}

		$new_path = \JFile::stripExt($path);

		// Rename filename
		if (\JFile::move($path, $new_path))
		{
			return $new_path;
		}

		return false;
	}

	/**
	 * Help method to encrypt and descypt sensitive data
	 *
	 * @return object
	 */
	public static function getCrypt()
	{
		$privateKey = md5(\JFactory::getConfig()->get('secret'));

		// Build the JCryptKey object.
		$key = new \JCryptKey('simple', $privateKey, $privateKey);

		// Setup the JCrypt object.
		return new \JCrypt(new \JCryptCipherSimple, $key);
	}
}