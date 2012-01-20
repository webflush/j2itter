<?php

/**
 * Thanks Federico Violante (federico.violante@gmail.com) by help in version 1.7 =)
 *
 * @Copyright Copyright (C) 2011 www.WebFlush.com.br
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * */
// No direct access
defined('_JEXEC') or die;
require_once( JPATH_SITE . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php' );
require_once(JPATH_SITE . DS . 'plugins' . DS . 'content' . DS . 'j2itter' . DS . 'twitteroauth' . DS . 'twitteroauth.php');

jimport('joomla.plugin.plugin');



/**
 * Joomla
 * @author              WebFlush - www.webflush.com.br
 * @package				Joomla
 * @subpackage          Content
 * @since				1.6
 */
class plgContentj2itter extends JPlugin {


	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 */
	function plgContentj2itter(&$subject, $params)
	{
		parent::__construct($subject, $params);
		$lang = JFactory::getLanguage();
		$lang->load('plg_content_j2itter', JPATH_ADMINISTRATOR);
	
	}
	


    /**
     *
     * Method is called right before content is saved into the database.
     * Article object is passed by reference, so any changes will be saved!
     * NOTE:  Returning false will abort the save with an error.
     * You can set the error by calling $article->setError($message)
     *
     * @param	string		The context of the content passed to the plugin.
     * @param	object		A JTableContent object
     * @param	bool		If the content is just about to be created
     * @return	bool		If false, abort the save
     * @since	1.6
     */
    public function onContentAfterSave($context, &$article, $isNew) {

        if ($context == "com_content.article") {


       $mainframe = JFactory::getApplication();
	   $consumer_key= $this->params->get('consumer_key', '');
	   $consumer_secret= $this->params->get('consumer_secret', '');
	   $oauth_token= $this->params->get('oauth_token', '');
	   $oauth_token_secret= $this->params->get('oauth_token_secret', '');
           
           $twitter_password = "";
             
            $conn = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);

            if ($conn) {

              $longUrl = JURI::root() . ContentHelperRoute::getArticleRoute($article->id, $article->catid);


              if ($isNew) {

                $tinyurl = $this->getTinyurl($longUrl);
                $msg = (substr($article->title, 0, 100) . " " . $tinyurl);
                $r = $conn->post('statuses/update', array('status' => $msg));
              } else {

                $tinyurl = $this->getTinyurl($longUrl);
                $msg = (" ".JText::_( 'PLG_CONTENT_J2ITTER_UPDATE' ) ." : " . substr($article->title, 0, 100) . " " . $tinyurl);
                $r = $conn->post('statuses/update', array('status' => $msg));
              }

	      $mainframe->enqueueMessage("J2itter - ".JText::_( 'PLG_CONTENT_J2ITTER_TWITTER_DONE' ) ."");

            return true;
          } else $mainframe->enqueueMessage("J2itter - ".JText::_( 'PLG_CONTENT_J2ITTER_CONECTION_ERROR' ) ."");
            
            return true;

        }
    }


      function getTinyurl($url) {
      $data = (trim(file_get_contents('http://tinyurl.com/api-create.php?url=' . $url)));
      if (!$data)
      return $url;
      return $data;
      }

}