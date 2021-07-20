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

use NRFramework\Cache;

defined('_JEXEC') or die('Restricted access');

/**
 *  Analytics Helper Class
 */
class Analytics
{
	/**
	 *  Returns average submission in current month
	 *
	 *  @return  float
	 */
	public static function getLeadsAverageThisMonth()
	{
		return number_format(self::getRows('thismonth') / date('d'), 1);
	}

	/**
	 *  Returns current month projection
	 *
	 *  @return  integer
	 */
	public static function getMonthProjection()
	{
		$days = (int) date('t');
		return self::getLeadsAverageThisMonth() * $days;
	}

	/**
	 *  Counts submissions
	 *
	 *  @param   string  $type    
	 *  @param   array   $params  
	 *
	 *  @return  integer            The number of found submissions
	 */
	public static function getRows($type = 'range', $params = array())
	{
		$hash = md5($type . serialize($params));

		if (Cache::has($hash))
		{
			return Cache::get($hash);
		}

	    $db = \JFactory::getDBO();

	    $query = $db->getQuery(true)
	 		->select('count(id)')
	        ->from($db->quoteName('#__convertforms_conversions'));

	    switch ($type)
	    {
	    	case 'range':
			    if (isset($params['startdate']))
			    {
			    	$query->where('date(created) >= ' . $db->quote($params['startdate']));
			    }
			    if (isset($params["enddate"]))
			    {
			    	$query->where('date(created) <= ' . $db->quote($params['enddate']));
			    }
				break;
				
	    	case 'thisyear':
	    		$query->where('YEAR(created) = ' . date('Y'));
				break;
				
	    	case 'lastyear':
	    		$query->where('YEAR(created) = ' . (date('Y')-1));
				break;
				
	    	case 'thismonth':
	    		$query->where('MONTH(created) = ' . date('m'));
	    		$query->where('YEAR(created) = ' . date('Y'));
				break;
				
	    	case 'lastmonth':
	    		$lastMonth = date('m', strtotime('first day of last month'));
	    		$lastMonthYear = date('Y', strtotime('first day of last month'));
	    		$query->where('MONTH(created) = ' . $lastMonth);
	    		$query->where('YEAR(created) = ' . $lastMonthYear);
				break;
				
	    	case "interval":
	    		$query->where('DATE(created) >= DATE(NOW()) - INTERVAL ' . $params['interval']);
				break;
				
	    	case "today":
	    		$query->where('DATE(created) = CURDATE()');
				break;
				
	    	case "yesterday":
	    		$query->where('DATE(created) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)');
	    		break;
	    }

		$db->setQuery($query);
		
	    return Cache::set($hash, number_format($db->loadResult()));
	}
}

?>