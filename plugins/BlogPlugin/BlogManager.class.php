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

      $query = "SELECT * FROM `codev_blog_table` ".
               "WHERE dest_user_id = $user_id ".
               "OR (dest_user_id = 0 AND dest_team_id IN ($formattedTeamList)) ".
               "ORDER BY date_submitted DESC";

      $result = SqlWrapper::getInstance()->sql_query($query);
      if (!$result) {
         echo "<span style='color:red'>ERROR: Query FAILED</span>";
         exit;
      }

      $postList = array();
      while($row = SqlWrapper::getInstance()->sql_fetch_object($result)) {
         $postList[$row->id] = BlogPostCache::getInstance()->getBlogPost($row->id, $row);
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