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
use Joomla\CMS\Uri\Uri;

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
        $image = $this->getImagePath();

        $this->setOgTags($title, $description, $image);
        $this->setTwitterTags($title, $description, $image);
        $this->setTags($title, $description, $image);
    }
    
    /**
     * Sets the Open Graph meta tags for page title, description, and type.
     * 
     * @param   string  $title        The title to set in og:title
     * @param   string  $description  The description to set in og:description
     * @param   string  $image        The image URL to set in og:image
     * 
     * @return  void
     */
    private function setOgTags($title, $description, $image)
    {
        $this->app->getDocument()->setMetaData('og:title', $title, 'property');
        $this->app->getDocument()->setMetaData('og:description', $description, 'property');
        $this->app->getDocument()->setMetaData('og:type', 'website', 'property');

        if ($image) {
            $this->app->getDocument()->setMetaData('og:image', $image, 'property');
        }
    }

    /**
     * Sets the Twitter meta tags for card type, title, and description.
     * 
     * @param   string  $title        The title to set in twitter:title
     * @param   string  $description  The description to set in twitter:description
     * @param   string  $image        The image URL to set in twitter:image
     * 
     * @return  void
     */
    private function setTwitterTags($title, $description, $image)
    {
        $this->app->getDocument()->setMetaData('twitter:card', 'summary_large_image', 'property');
        $this->app->getDocument()->setMetaData('twitter:title', $title, 'property');
        $this->app->getDocument()->setMetaData('twitter:description', $description, 'property');

        if ($image) {
            $this->app->getDocument()->setMetaData('twitter:image', $image, 'property');
        }
    }

    /**
     * Sets the standard meta tags for page title, description, and image.
     * 
     * @param   string  $title        The title to set in meta title
     * @param   string  $description  The description to set in meta description
     * @param   string  $image        The image URL to set in meta image
     * 
     * @return  void
     */
    private function setTags($title, $description, $image)
    {
        $this->app->getDocument()->setMetaData('title', $title, 'property');
        $this->app->getDocument()->setMetaData('description', $description, 'property');  

        if($image) {
            $this->app->getDocument()->setMetaData('image', $image, 'property');
        }
    }

    /**
     * Retrieves the URL path of the image thumbnail if it exists on the server.
     * 
     * @return  string
     */
    private function getImagePath()
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        $model = $this->getModel();
        $table = $model->getTable();
        $listId = $table->get('id');

        if (!$listId) {
            return;
        }

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