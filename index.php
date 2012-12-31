<?php
require('include/session.inc.php');

/*
   This file is part of CoDev-Timetracking.

   CoDev-Timetracking is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   CoDev-Timetracking is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with CoDev-Timetracking.  If not, see <http://www.gnu.org/licenses/>.
*/

require('path.inc.php');

// check if INSTALL needed
if (!file_exists(Constants::$config_file)) {

   // check if old config files exist (0.99.17 and before)
   if ((!file_exists('constants.php')) || (!file_exists('include/mysql_config.inc.php'))) {
      // not found, normal install
      header('Location: install/install.php');
   } else {
      // convert to config.ini (0.99.18)
      header('Location: tools/convert_config_file.php');
   }

   exit;
}

class IndexController extends Controller {

   /**
    * Initialize complex static variables
    * @static
    */
   public static function staticInit() {
      // Nothing special
   }

   protected function display() {
      // Drifted tasks
      if(Tools::isConnectedUser()) {

         // updateBacklog DialogBox
         if (isset($_POST['bugid'])) {
            $bugid = Tools::getSecurePOSTStringValue('bugid');
            $backlog = Tools::getSecurePOSTStringValue('backlog', '');
            $issue = IssueCache::getInstance()->getIssue($bugid);
            $issue->setBacklog($backlog);
         }

         $driftedTasks = $this->getIssuesInDrift();
         if(isset($driftedTasks)) {
            $this->smartyHelper->assign('driftedTasks', $driftedTasks);
         }

         // Consistency errors
         $consistencyErrors = $this->getConsistencyErrors();

         // no specific Mgr errors right now
         #$consistencyErrorsMgr = $this->getConsistencyErrorsMgr($this->session_user);
         #$consistencyErrors = array_merge($consistencyErrors, $consistencyErrorsMgr);

         if(count($consistencyErrors) > 0) {
            $this->smartyHelper->assign('consistencyErrorsTitle', count($consistencyErrors).' '.T_("Errors in your Tasks"));
            $this->smartyHelper->assign('consistencyErrors', $consistencyErrors);
         }
      }
   }

   /**
    * Get issues in drift
    * @param User $this->session_user
    * @return mixed[]
    */
   private function getIssuesInDrift() {

      // get all teams except those where i'm Observer
      #$dTeamList = $this->session_user->getDevTeamList();
      #$mTeamList = $this->session_user->getManagedTeamList();
      #$teamList = $dTeamList + $mTeamList;           // array_merge does not work ?!

      $teamList = array($this->teamid => $this->teamList[$this->teamid]);

      // except disabled projects
      $projList = $this->session_user->getProjectList($teamList, true, false);

      $allIssueList = $this->session_user->getAssignedIssues($projList);
      $issueList = array();
      $driftedTasks = array();

      foreach ($allIssueList as $issue) {
         $driftEE = $issue->getDrift();
         if ($driftEE >= 1) {
            $issueList[] = $issue;
         }
      }
      if (count($issueList) > 0) {
         foreach ($issueList as $issue) {
            // TODO: check if issue in team project list ?
            $driftEE = $issue->getDrift();

            $formatedTitle = $issue->getFormattedIds();
            $formatedSummary = str_replace("'", "\'", $issue->getSummary());
            $formatedSummary = str_replace('"', "\'", $formatedSummary);

            $driftedTasks[] = array('issueInfoURL' => Tools::issueInfoURL($issue->getId()),
               'projectName' => $issue->getProjectName(),
               'driftEE' => $driftEE,
               'formatedTitle' => $formatedTitle,
               'bugId' => $issue->getId(),
               'backlog' => $issue->getBacklog(),
               'formatedSummary' => $formatedSummary,
               'summary' => $issue->getSummary());
         }
      }

      return $driftedTasks;
   }

   /**
    * Get consistency errors
    * @return mixed[]
    */
   private function getConsistencyErrors() {
      $consistencyErrors = array(); // if null, array_merge fails !

      #$teamList = $this->teamList;
      $teamList = array($this->teamid => $this->teamList[$this->teamid]);

      // except disabled projects
      $projList = $this->session_user->getProjectList($teamList, true, false);

      $issueList = $this->session_user->getAssignedIssues($projList, true);

      $ccheck = new ConsistencyCheck2($issueList);

      $cerrList = $ccheck->check();

      if (count($cerrList) > 0) {
         foreach ($cerrList as $cerr) {
            if ($this->session_user->getId() == $cerr->userId) {
               $issue = IssueCache::getInstance()->getIssue($cerr->bugId);
               $titleAttr = array(
                   T_('Project') => $issue->getProjectName(),
                   T_('Summary') => $issue->getSummary(),
               );
               $consistencyErrors[] = array('issueURL' => Tools::issueInfoURL($cerr->bugId, $titleAttr),
                  'status' => Constants::$statusNames[$cerr->status],
                  'desc' => $cerr->desc);
            }
         }
      }

      return $consistencyErrors;
   }

   /**
    * managers get some more consistencyErrors
    * @param User $this->session_user
    * @return mixed[]
    */
   private function getConsistencyErrorsMgr() {
      $consistencyErrors = array(); // if null, array_merge fails !
/*
      $mTeamList = array_keys($this->session_user->getManagedTeamList());
      $lTeamList = array_keys($this->session_user->getLeadedTeamList());
      $teamList = array_merge($mTeamList, $lTeamList);

      $issueList = array();
      foreach ($teamList as $teamid) {
         $issues = TeamCache::getInstance()->getTeam($teamid)->getTeamIssueList(true, false);
         $issueList = array_merge($issueList, $issues);
      }
*/
      // nothing to check right now...

      return $consistencyErrors;
   }

}

// ========== MAIN ===========
IndexController::staticInit();
$controller = new IndexController(Constants::$homepage_title,'index');
$controller->execute();

?>
