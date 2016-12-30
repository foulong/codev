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

/**
 * Blog post structure
 */
class BlogPost implements Comparable {

   const severity_low = 1;
   const severity_normal = 2;
   const severity_high = 3;

   /**
    * @var Logger The logger
    */
   private static $logger;

   /**
    * Initialize complex static variables
    * @static
    */
   public static function staticInit() {
      self::$logger = Logger::getLogger(__CLASS__);
   }

   public $id;
   public $src_user_id;
   public $dest_user_id;
   public $dest_team_id;
   public $severity;
   public $category;
   public $summary;
   public $content;
   public $date_submitted;
   public $date_expire;
   public $color;

   private $activityList;

   public function __construct($post_id, $details = NULL) {
      $this->id = $post_id;
      $this->initialize($details);
   }

   private function initialize($row = NULL) {
      if(NULL == $row) {
         $query = "SELECT * FROM `codev_blog_table` WHERE id = ".$this->id.";";
         $result = SqlWrapper::getInstance()->sql_query($query);
         if (!$result) {
            echo "<span style='color:red'>ERROR: Query FAILED</span>";
            exit;
         }
         $row = SqlWrapper::getInstance()->sql_fetch_object($result);
      }

      $this->date_submitted  = $row->date_submitted;
      $this->src_user_id = $row->src_user_id;
      $this->dest_user_id = $row->dest_user_id;
      $this->dest_team_id = $row->dest_team_id;
      $this->severity = $row->severity;
      $this->category = $row->category;
      $this->summary = $row->summary;
      $this->content = $row->content;
      $this->date_expire = $row->date_expire;
      $this->color = $row->color;

      #$this->activityList = $this->getActivityList();
   }

   /**
    * Literal name for the given severity id
    *
    * @param int $severity
    * @return string severityName or NULL if unknown
    */
   public static function getSeverityName($severity) {
      switch ($severity) {
         case self::severity_low:
            return T_('Low');
         case self::severity_normal:
            return T_('Normal');
         case self::severity_high:
            return T_('High');
         default:
            #return T_("unknown $severity");
            return NULL;
      }
   }

   /**
    * create a new post
    *
    * @param int $src_user_id
    * @param int $severity
    * @param string $category
    * @param string $summary
    * @param string $content
    * @param int $dest_user_id
    * @param int $dest_project_id
    * @param int $dest_team_id
    * @param int $date_expire
    * @param int $color
    *
    * @return blogPost id or '0' if failed
    */
   public static function create($src_user_id, $severity, $category, $summary, $content,
         $dest_user_id=0, $dest_project_id=0, $dest_team_id=0,
         $date_expire=0, $color=0) {

      // format values to avoid SQL injections
      $fSeverity = SqlWrapper::sql_real_escape_string($severity);
      $fCategory = SqlWrapper::sql_real_escape_string($category);
      $fSummary = SqlWrapper::sql_real_escape_string($summary);
      $fContent = SqlWrapper::sql_real_escape_string($content);
      $fDateExpire = SqlWrapper::sql_real_escape_string($date_expire);

      $date_submitted = time(); # mktime(0, 0, 0, date('m'), date('d'), date('Y'));

      $query = "INSERT INTO `codev_blog_table` ".
               "(`date_submitted`, `src_user_id`, `dest_user_id`, `dest_project_id`, `dest_team_id`, ".
               "`severity`, `category`, `summary`, `content`, `date_expire`, `color`) ".
               "VALUES ('$date_submitted','$src_user_id','$dest_user_id','$dest_project_id','$dest_team_id',".
               "'$fSeverity','$fCategory','$fSummary','$fContent','$fDateExpire','$color');";

      $result = SqlWrapper::getInstance()->sql_query($query);
      if (!$result) {
         echo "<span style='color:red'>ERROR: Query FAILED</span><br>";
         return 0;
      }

      return SqlWrapper::getInstance()->sql_insert_id();
   }

   /**
    * Delete a post and all it's activities.
    *
    * Note: Only administrators & the owner of the post are allowed to delete.
    *
    * @param int $blogPost_id
    */
   public static function delete($blogPost_id) {
      // TODO check admin/ user access rights

      $query = "DELETE FROM `codev_blog_activity_table` WHERE blog_id = ".$blogPost_id.";";
      $result = SqlWrapper::getInstance()->sql_query($query);
      if (!$result) {
         echo "<span style='color:red'>ERROR: Query FAILED</span>";
         exit;
      }

      $query = "DELETE FROM `codev_blog_table` WHERE id = ".$blogPost_id.";";
      $result = SqlWrapper::getInstance()->sql_query($query);
      if (!$result) {
         echo "<span style='color:red'>ERROR: Query FAILED</span>";
         exit;
      }
   }

   /**
    * @param int $blogPost_id
    * @param int $user_id
    * @param string $action
    * @param int $date
    *
    * @return int activity id or '0' if failed
    */
   public static function addActivity($blogPost_id, $user_id, $action, $date) {
      // check if $blogPost_id exists (foreign keys do not exist in MyISAM)
      $fPostId = SqlWrapper::sql_real_escape_string($blogPost_id);

      $query = "SELECT id FROM `codev_blog_table` where id = ".$fPostId.";";
      $result = SqlWrapper::getInstance()->sql_query($query);
      if (!$result) {
         echo "<span style='color:red'>ERROR: Query FAILED</span>";
         exit;
      }
      if (0 == SqlWrapper::getInstance()->sql_num_rows($result)) {
         self::$logger->error("addActivity: blogPost '$fPostId' does not exist !");
         return 0;
      }

      // add activity
      $fUserId = SqlWrapper::sql_real_escape_string($user_id);
      $fAction = SqlWrapper::sql_real_escape_string($action);
      $fDate   = SqlWrapper::sql_real_escape_string($date);
      $query = "INSERT INTO `codev_blog_activity_table` ".
               "(`blog_id`, `user_id`, `action`, `date`) ".
               "VALUES ('$fPostId','$fUserId','$fAction','$fDate')";

      $result = SqlWrapper::getInstance()->sql_query($query);
      if (!$result) {
         echo "<span style='color:red'>ERROR: Query FAILED</span>";
         return 0;
      }

      return SqlWrapper::getInstance()->sql_insert_id();
   }

   /**
    * @return BlogActivity[]
    */
   public function getActivityList() {
      if (NULL == $this->activityList) {
         $query = "SELECT * FROM `codev_blog_activity_table` WHERE blog_id = $this->id ORDER BY date DESC";
         $result = SqlWrapper::getInstance()->sql_query($query);
         if (!$result) {
            echo "<span style='color:red'>ERROR: Query FAILED</span>";
            exit;
         }

         $this->activityList = array();
         while($row = SqlWrapper::getInstance()->sql_fetch_object($result)) {
            $this->activityList[$row->id] = new BlogActivity($row->id, $row->blog_id, $row->user_id, $row->action, $row->date);
         }
      }
      return $this->activityList;
   }

   /**
    * criteria: date_submission, date_expired, severity
    *
    * @param BlogPost $postB the object to compare to
    *
    * @return 1 if $postB higher priority, -1 if lower, 0 if equal
    */
   public static function compare(Comparable $postA, Comparable $postB) {
      // TODO
      return 0;
   }

}
BlogPost::staticInit();