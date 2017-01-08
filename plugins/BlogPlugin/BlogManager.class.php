<?php
/*
   This file is part of CodevTT

   CodevTT is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   CodevTT is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with CodevTT.  If not, see <http://www.gnu.org/licenses/>.
*/

class BlogManager {

   const OPTION_FILTER_RECIPIENT = 'recipient';
   const OPTION_FILTER_CATEGORY = 'category';
   const OPTION_FILTER_SEVERITY = 'severity';
   const OPTION_FILTER_DISPLAY_HIDDEN_POSTS = 'isDisplayHiddenPosts';


   private static $logger;
   
   private $categoryList;
   private $severityList;

   public function __construct() {
   }
   public static function staticInit() {
      self::$logger = Logger::getLogger(__CLASS__);
   }

   /**
    * available categories are stored in codev_config_table.
    * @return string[] (id => name)
    */
   public function getCategoryList() {
      if (NULL == $this->categoryList) {
         $this->categoryList = Config::getValue(Config::id_blogCategories);
         ksort($this->categoryList);
      }
      return $this->categoryList;
   }

   /**
    * available severity values
    * @return string[] (id => name)
    */
   public function getSeverityList() {
      if (NULL == $this->severityList) {
         $this->severityList = array();

         for ($i = 0; $i < 10; $i++) {
            $sevName =  BlogPost::getSeverityName($i);
            if (NULL != $sevName) {
               $this->severityList[$i] = $sevName;
            }
         }
      }
      return $this->severityList;
   }

   /**
    * Get user options
    *
    * Note: settings will not be saved in dashboardSettings, we want them to be
    *       more persistent. otherwise, removing the plugin from the dashboard looses
    *       the settings.
    *
    * @param type $userId
    * @return type
    */
   private function getUserOptions($userId, $reload = FALSE) {

      //if (NULL == $this->userOptions || $reload) {

         // set default values (if new options are added, user may not have them)
         $userOptions = array(
             BlogManager::OPTION_FILTER_RECIPIENT => 'all', // 'all', 'current_team', 'only_me'
             BlogManager::OPTION_FILTER_CATEGORY => 0, // all
             BlogManager::OPTION_FILTER_SEVERITY => 0, // all
             BlogManager::OPTION_FILTER_DISPLAY_HIDDEN_POSTS => 0, // hide
         );

         // override default values with user settings
         $userOptionsJson = Config::getValue(Config::id_blogPluginOptions, array($userId, 0, 0, 0, 0, 0), true);
         if(null != $userOptionsJson) {
            $options = json_decode($userOptionsJson, true);
            foreach ($options as $key => $value) {
               $userOptions[$key] = $value;
            }
         }
      //}
      return $userOptions;
   }


   /**
    * return the posts to be displayed for a given user,
    * depending on it's [userid, teams, projects] and personal filter preferences.
    *
    * we want:
    * - all posts assigned to the user
    * - all posts assigned to a team where the user is member
    * - all posts assigned to a project that is in one of the user's teams
    *
    * @param int $user_id
    *
    * @return BlogPost[]
    */
   public function getPosts($user_id) {
      $user = UserCache::getInstance()->getUser($user_id);
      $teamList = $user->getTeamList();

      $formattedTeamList = implode(',', array_keys($teamList));

      $userOptions = $this->getUserOptions($user_id);

      $query = "SELECT blog_tab.* FROM `codev_blog_table` AS blog_tab ";

//      if (0 == $userOptions[BlogManager::OPTION_FILTER_DISPLAY_HIDDEN_POSTS]) {
//         $query .= "LEFT JOIN `codev_blog_activity_table` AS activity_tab ON blog_tab.id = activity_tab.blog_id ";
//      }

      // TODO use BlogManager::OPTION_FILTER_RECIPIENT
      $query .= "WHERE blog_tab.dest_user_id = $user_id ".
               "OR (blog_tab.dest_user_id = 0 AND blog_tab.dest_team_id IN ($formattedTeamList)) ";

      if (0 != $userOptions[BlogManager::OPTION_FILTER_CATEGORY]) {
         $query .= 'AND blog_tab.category = '.$userOptions[BlogManager::OPTION_FILTER_CATEGORY].' ';
      }
      if (0 != $userOptions[BlogManager::OPTION_FILTER_SEVERITY]) {
         $query .= 'AND blog_tab.severity = '.$userOptions[BlogManager::OPTION_FILTER_SEVERITY].' ';
      }
//      if (0 == $userOptions[BlogManager::OPTION_FILTER_DISPLAY_HIDDEN_POSTS]) {
//         $query .= "AND activity_tab.user_id = $user_id AND activity_tab.action = ".BlogPost::actionType_hide.' ';
//      }
      $query .= "ORDER BY date_submitted DESC";

      $result = SqlWrapper::getInstance()->sql_query($query);
      if (!$result) {
         echo "<span style='color:red'>ERROR: Query FAILED</span>";
         exit;
      }

      $postList = array();
      while($row = SqlWrapper::getInstance()->sql_fetch_object($result)) {
         
         // TODO replace this DISPLAY_HIDDEN_POSTS filter by a better SQL query,
         // it is stupid to instantiate BlogPosts that we do not want...
         $bpost = BlogPostCache::getInstance()->getBlogPost($row->id, $row);
         if ((!$bpost->isHidden($user_id)) ||
             (1 == $userOptions[BlogManager::OPTION_FILTER_DISPLAY_HIDDEN_POSTS])) {
            $postList[$row->id] = $bpost;
         }
      }

      return $postList;
   }

   /**
    * return the posts submitted by a given user,
    *
    * @param int $user_id
    *
    * @return BlogPost[]
    */
   public function getSubmittedPosts($user_id) {
      $query = "SELECT * FROM `codev_blog_table` where src_user_id = ".$user_id.";";
      $result = SqlWrapper::getInstance()->sql_query($query);
      if (!$result) {
         echo "<span style='color:red'>ERROR: Query FAILED</span>";
         exit;
      }

      $submittedPosts = array();
      while($row = SqlWrapper::getInstance()->sql_fetch_object($result)) {
         $submittedPosts[$row->id] = BlogPostCache::getInstance()->getBlogPost($row->id, $row);
      }

      return $submittedPosts;
   }

}
BlogManager::staticInit();