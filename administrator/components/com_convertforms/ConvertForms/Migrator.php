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

/**
 *  Convert Forms Migrator
 *
 *  The purpose of this class is to migrate data and offer a non-breaking update from 1.0 to 2.0 version.
 */
class Migrator
{
	/**
	 *  Save migration logs
	 *
	 *  @var  array
	 */
	public $log;

	/**
	 *  Override the database object for testing purposes and sharing data between different databases
	 *
	 *  @var  mixed
	 */
	private $db;

	/**
	 *  Class Constructor
	 *
	 *  @param  mixed    $db          Database options
	 */
	public function __construct($db = null)
	{
		$this->db = $db;
		\JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_convertforms/tables');
	}

	/**
	 *  Start migration process
	 *
	 *  @return  void
	 */
	public function start()
	{
		$this->migrateForms();
		$this->migrateLeads();
		$this->saveLog();
	}

	/**
	 *  Start the migration of all available forms
	 *
	 *  @return  void
	 */
	public function migrateForms()
	{
		$this->log[] = 'Forms: Migration started.';

		if (!$forms = $this->getForms())
		{
			return;
		}

		foreach ($forms as $key => $form)
		{
			$this->migrateForm($form->id);
		}

		$this->log[] = 'Forms: Migration completed.';
	}

	/**
	 *  Move email column value to params
	 *
	 *  @return  void
	 */
	public function migrateLeads()
	{
		$this->log[] = 'Leads: Migration started.';

		$db    = $this->getDB();
		$query = $db->getQuery(true);

		$query
			->select('*')
			->from('#__convertforms_conversions');

		$db->setQuery($query);

		$leads = $db->loadObjectList();

		$this->log[] = 'Leads: Found ' . count($leads) . ' leads';

		$leads_updated = 0;

		foreach ($leads as $key => $lead)
		{
			$params = json_decode($lead->params, true);

			if (isset($params['email']))
			{
				continue;
			}

			$params['email'] = $lead->email;

			$table = \JTable::getInstance('Conversion', 'ConvertFormsTable');
			$table->load($lead->id);

			$table->params = json_encode($params);

			if (!$table->store())
			{
				$this->log[] = 'Leads: Cannot store params to table.';
			}

			$leads_updated++;
		}

		$this->log[] = 'Leads: ' . $leads_updated . ' rows updated.';
		$this->log[] = 'Leads: Migration completed.';
	}
	
	/**
	 *  Migrate a form
	 *
	 *  @param   integer  $formid  The form's id
	 *
	 *  @return  boolean              
	 */
	public function migrateForm($formid, $to_formid = null)
	{
		// Load Form
		if (!$form = $this->getForm($formid))
		{
			$this->log[] = 'Form not found';
			return;
		}

		$params = json_decode($form->params);

		// Capture the maximum value of the key property in order to use on newly appended items
		$maxkey = 0;
		$countFields = count((array) $params->fields);

		if (isset($params->fields))
		{
			foreach ($params->fields as $key => $field)
			{
				// Fields Migration v2 to v3
				// If 'key' property exists then the form's fields are already migrated.
				if (!isset($field->key))
				{
					// Let's migrate
					$field->key = (int) str_replace('fields', '', $key);
					$field->hidelabel = $params->hidelabels;

					if ($params->formlayout == 'col2')
					{
						$field->cssclass = 'cf-one-half';
					}

					if ($params->formlayout == 'hor')
					{
						switch ($countFields)
						{
							case 1:
								$cl_ = 'cf-two-thirds';
								break;
							case 3:
								$cl_ = 'cf-one-fourth';
								break;
							default:
								$cl_ = 'cf-one-third';
						}

						$field->cssclass = $cl_;
					}

					if (in_array($field->type, array('dropdown', 'checkbox')))
					{
						$field->choices = $field->dropdownoptions;
						unset($field->dropdownoptions);
					}

					$this->log[] = "Field $field->name migrated";
				}

				$maxkey = $field->key > $maxkey ? (int) $field->key : $maxkey;
			}
		}

		// Button migration. Move button settings to Fields property.
		if (isset($params->btnalign))
		{
			$buttonKey = 'fields' . ($maxkey + 1);

			// Submit button size
			if ($params->formlayout == 'hor')
			{
				$params->classsuffix .= ' cf-hor';
			}

			unset($params->submitsize);
			unset($params->formlayout);

			$params->fields->$buttonKey = (object) [
                'key'            => $buttonKey,
                'type'           => 'submit',
                'text'           => $params->btntext,
				'align'          => $params->btnalign,
				'btnstyle'       => $params->btnstyle,
				'fontsize'       => $params->btnfontsize,
				'shadow'         => $params->btnshadow,
				'bg'             => $params->btnbg,
				'textcolor'      => $params->btntextcolor,
				'texthovercolor' => $params->btntexthovercolor,
				'borderradius'   => $params->btnborderradius,
				'vpadding'       => $params->btntvpadding,
				'hpadding'       => $params->btnhpadding,
				'size' 		     => ($params->btnalign != 'full') ? 'cf-width-auto' : '',
				'cssclass'       => ($countFields == 2) ? 'cf-one-third' : 'cf-one-fourth'
			];

			unset($params->btnalign);
			unset($params->btnstyle);
			unset($params->btntext);
			unset($params->btnfontsize);
			unset($params->btnshadow);
			unset($params->btnbg);
			unset($params->btntextcolor);
			unset($params->btntexthovercolor);
			unset($params->btnborderradius);
			unset($params->btntvpadding);
			unset($params->btnhpadding);

			$this->log[] = 'Submit button migrated';
		}

		// Is Centered Options
		if (isset($params->centerform) && $params->centerform == '1')
		{
			$params->classsuffix .= ' cf-iscentered';
			unset($params->centerform);
		}

		$params->classsuffix = trim($params->classsuffix);

		// Save changed back to database
		$table = \JTable::getInstance('Form', 'ConvertFormsTable');
		$table->load($to_formid ?: $form->id);

		$table->params = json_encode($params);

		if (!$table->store())
		{
			$this->log[] = 'Params: Cannot store params to table.';
		}
	}

	/**
	 *  Returns a database instance
	 *
	 *  @return  JDatabase object
	 */
	private function getDB()
	{
		return is_array($this->db) ? \JDatabaseDriver::getInstance($this->db) : \JFactory::getDBo();
	}
	
	/**
	 *  Get form's data from the database
	 *
	 *  @param   Integer  $id  Forms's ID
	 *
	 *  @return  Object        Form's data
	 */
	public function getForm($id)
	{
		$db    = $this->getDB();
		$query = $db->getQuery(true);

		$query
			->select('*')
			->from('#__convertforms')
			->where('id = ' . $id);

		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 *  Get Forms List
	 *
	 *  @return  object
	 */
	public function getForms()
	{
		$db    = $this->getDB();
		$query = $db->getQuery(true);

		$query
			->select('*')
			->from('#__convertforms');

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 *  Save log to a file
	 *
	 *  @return  void
	 */
	public function saveLog()
	{
		if (!is_array($this->log))
		{
			return;
		}

		array_unshift(
			$this->log,
			str_repeat('-', 19),
			\JFactory::getDate(),
			str_repeat('-', 19)
		);

		$log = implode(PHP_EOL, $this->log) . PHP_EOL . PHP_EOL;

		try
		{
			$logFile = __DIR__ . '/migrator.log';

			if (method_exists('JFile', 'append'))
			{
				// Joomla 3.6.0+
				\JFile::append($logFile, $log);
			} else
			{
				\JFile::write($logFile, $log);
			}
		}
		catch (Exception $e)
		{
			$this->log[] = "Can't write log file" . $e->getMessage();
		}
	}
}

?>