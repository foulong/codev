<?php
require('../include/session.inc.php');
require('../path.inc.php');

// Note: i18n is included by the Controler class, but Ajax dos not use it...
require_once('i18n/i18n.inc.php');

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



// =========== MAIN ==========
$logger = Logger::getLogger("sendTimetrackEmails");


if (1 == Constants::$emailSettings['enable_email_notification']) {

   $team_id = 20; // RSI_TMA_Sante

   //$startT = strtotime("first day of last month");
   //$endT = strtotime("-1 days", time());
   $endT = time();
   $endT = mktime(0, 0, 0, date('m', $endT), date('d',$endT), date('Y', $endT));


   $query = "SELECT id FROM `codev_team_table` WHERE enabled = 1;";

   $result = SqlWrapper::getInstance()->sql_query($query);
   if (!$result) {
      echo "<span style='color:red'>ERROR: Query FAILED</span>";
      exit;
   }

   while($row = SqlWrapper::getInstance()->sql_fetch_object($result)) {
      $team = TeamCache::getInstance()->getTeam($row->id);
      echo "=== Team $row->id : ".$team->getName()."<br>";
      $team->sendTimesheetEmails($startT, $endT);
   }






   #$user_id = 2; // lbayle
   #$user = UserCache::getInstance()->getUser($user_id);
   #$user->sendTimetrackEmails($team_id, $startT, $endT);

}