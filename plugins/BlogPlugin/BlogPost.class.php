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

   const actionType_ack  = 0;
   const actionType_hide = 1;

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
         if (0 == SqlWrapper::getInstance()->sql_num_rows($result)) {
            $e = new Exception("BlogPost $this->id does not exist");
            self::$logger->error("EXCEPTION BlogPost constructor: " . $e->getMessage());
            self::$logger->error("EXCEPTION stack-trace:\n" . $e->getTraceAsString());
            throw $e;
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
    * Literal name for the given activity_id
    *
    * @param int $actionType
    * @return string actionName or NULL if unknown
    */
   public static function getActionName($actionType) {
      switch ($actionType) {
         case self::actionType_ack:
            return T_('Acknowledged');
         case self::actionType_hide:
            return T_('Hidden');
         default:
            #return T_('unknown');
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
    */
   public function delete() {
      // TODO check admin/ user access rights

      $query = "DELETE FROM `codev_blog_activity_table` WHERE blog_id = ".$this->id.";";
      $result = SqlWrapper::getInstance()->sql_query($query);
      if (!$result) {
         echo "<span style='color:red'>ERROR: Query FAILED</span>";
         exit;
      }

      $query2 = "DELETE FROM `codev_blog_table` WHERE id = ".$this->id.";";
      $result2 = SqlWrapper::getInstance()->sql_query($query2);
      if (!$result2) {
         echo "<span style='color:red'>ERROR: Query FAILED</span>";
         exit;
      }
   }

   /**
    * Creates an activity for a user
    *
    * @param int $user_id
    * @param int $actionType
    * @param boolean $value  true to add, false to remove
    * @param int $date
    *
    * @throws exception if failed
    */
   public function setAction($user_id, $actionType, $value, $date) {

      // TODO: check if activity_id is valid ?
      
      if (true === $value) {
         // set this action.
         // TODO if already set, do nothing
         $query = "INSERT INTO `codev_blog_activity_table` (`blog_id`, `user_id`, `action`, `date`) ".
                  "VALUES ('$this->id','$user_id','$actionType','$date')";
      } else {
         // unset the action
         $query = "DELETE FROM `codev_blog_activity_table` WHERE user_id = $user_id AND action = $actionType";
      }

      $result = SqlWrapper::getInstance()->sql_query($query);
      if (!$result) {
         #echo "<span style='color:red'>ERROR: Query FAILED</span>";
         $e = new Exception("ERROR: Query FAILED");
         self::$logger->error("EXCEPTION BlogPost addActivity: " . $e->getMessage());
         self::$logger->error("EXCEPTION stack-trace:\n" . $e->getTraceAsString());
         throw $e;
      }
      return SqlWrapper::getInstance()->sql_insert_id();
   }

   /**
    * @return array activities[]
    */
   public function getActivityList() {
      if (NULL == $this->activityList) {
         $query = "SELECT * FROM `codev_blog_activity_table` WHERE blog_id = $this->id ";
         $query .= "ORDER BY date DESC";
         $result = SqlWrapper::getInstance()->sql_query($query);
         if (!$result) {
            echo "<span style='color:red'>ERROR: Query FAILED</span>";
            exit;
         }

         $this->activityList = array();
         while($row = SqlWrapper::getInstance()->sql_fetch_object($result)) {

            $user = UserCache::getInstance()->getUser($row->user_id);

            $this->activityList[$row->id] = array(
                'id' => $row->id,
                'blogpostId' => $row->blog_id,
                'userId' => $row->user_id,
                'actionType' => $row->action,
                'timestamp' => $row->date,
                
                'userName' => $user->getName(),
                'actionName' => self::getActionName($row->action),
                'formatedDate' => date("Y-m-d G:i", $row->date), // TODO 1971-01-01
                );
         }
      }

      // TODO set filter on activity type

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