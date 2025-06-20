<?php
/**
 * List's Metadata
 *
 * @package     Joomla.Plugin
 * @subpackage  Fabrik.list.metadata
 * @copyright   Copyright (C) 2025 Jlowcode Org - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;

/**
 * 	Plugin that displays relevant information from a list when its URL is shared
 * 
 * @package     	Joomla.Plugin
 * @subpackage  	Fabrik.list.metadata
 */
class PlgFabrik_ListMetadata extends PlgFabrik_List {

    public function __construct(&$subject, $config = array()) 
    {
        parent::__construct($subject, $config);
    }

    /**
     * Check user can view the read only element OR view in list view
     *
     * @param   	String 		$view 		View list/form
     *
     * @return  	bool
     */
    public function canView($view = 'list') 
    {
        return true;
    }

    /**
     * Increments the access (views) counter each time the record is loaded.
     * 
     * @return 		void
    */
    public function onLoadData(&$args) 
    {
        $model = $this->getModel();
        $table = $model->getTable();

        $title = $table->get('label');
        $description = $table->get('introduction');

        $this->setOgTags($title, $description);
        $this->setTwitterTags($title, $description);
        $this->app->getDocument()->setMetaData('description', strip_tags($description));

        $image = $this->getImagePath();
        if ($image) {
            $this->app->getDocument()->setMetaData('og:image', $image);
            $this->app->getDocument()->setMetaData('twitter:image', $image);
        }

    }
    
    /**
     * Sets the Open Graph meta tags for page title, description, and type.
     * 
     * @param   string  $title        The title to set in og:title
     * @param   string  $description  The description to set in og:description
     * 
     * @return  void
     */
    public function setOgTags($title, $description)
    {
        $this->app->getDocument()->setMetaData('og:title', strip_tags($title));
        $this->app->getDocument()->setMetaData('og:description', strip_tags($description));
        $this->app->getDocument()->setMetaData('og:type', 'website');
    }

    /**
     * Sets the Twitter meta tags for card type, title, and description.
     * 
     * @param   string  $title        The title to set in twitter:title
     * @param   string  $description  The description to set in twitter:description
     * 
     * @return  void
     */
    public function setTwitterTags($title, $description)
    {
        $this->app->getDocument()->setMetaData('twitter:card', 'summary_large_image');
        $this->app->getDocument()->setMetaData('twitter:title', strip_tags($title));
        $this->app->getDocument()->setMetaData('twitter:description', strip_tags($description));
    }

    /**
     * Retrieves the URL path of the image thumbnail if it exists on the server.
     * 
     * @return  string
     */
    public function getImagePath()
    {
        $model = $this->getModel();
        $table = $model->getTable();
        $listId = $table->get('id');

        if (!$listId) {
            return;
        }

        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select($db->qn('miniatura'))->from($db->qn('adm_cloner_listas'))->where($db->qn('id_lista') . ' = ' . $db->q($listId));
        $db->setQuery($query);
        $thumb = $db->loadResult();

        if ($thumb) {
            $physicalPath = JPATH_SITE . '/' . ltrim($thumb, '/');

            if (file_exists($physicalPath)) {
                return Uri::root() . ltrim($thumb, '/');
            }
        }
    }

}