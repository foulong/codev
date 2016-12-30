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

class BlogActivity {

   private static $logger;

   const action_ack  = 0;
   const action_hide = 1;

   public $id;
   public $blogPost_id;
   public $user_id;
   public $action;
   public $date;

   public static function staticInit() {
      self::$logger = Logger::getLogger(__CLASS__);
   }

   public function __construct($id, $blogPost_id, $user_id, $action, $date) {
      $this->id = $id;
      $this->blogPost_id = $blogPost_id;
      $this->user_id = $user_id;
      $this->action = $action;
      $this->date = $date;
   }

   /**
    * Literal name for the given action id
    *
    * @param int $action
    * @return string actionName or NULL if unknown
    */
   public static function getActionName($action) {
      switch ($action) {
         case self::action_ack:
            return T_('Acknowledged');
         case self::action_hide:
            return T_('Hidden');
         default:
            #return T_('unknown');
            return NULL;
      }
   }

}
BlogActivity::staticInit();
