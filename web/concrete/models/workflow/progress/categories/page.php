<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class PageWorkflowProgress extends WorkflowProgress {  
	
	protected $cID;

	public static function add(Workflow $wf, PageWorkflowRequest $wr) {
		$wp = parent::add($wf, $wr);
		$db = Loader::db();
		$db->Replace('PageWorkflowProgress', array('cID' => $wr->getRequestedPageID(), 'wpID' => $wp->getWorkflowProgressID()), array('cID', 'wpID'), true);
		$wp->cID = $wr->getRequestedPageID();
		return $wp;
	}
	
	public static function getByID($wpID) {
		$db = Loader::db();
		$wp = parent::getByID($wpID);
		$row = $db->GetRow('select cID from PageWorkflowProgress where wpID = ?', array($wpID));
		$wp->setPropertiesFromArray($row);
		return $wp;
	}
	
	public function delete() {
		parent::delete();
		$db = Loader::db();
		$db->Execute('delete from PageWorkflowProgress where wpID = ?', array($this->wpID));
	}
	
	public static function getList(Page $c) {
		$db = Loader::db();
		$r = $db->Execute('select wpID from PageWorkflowProgress where cID = ?', array($c->getCollectionID()));
		$list = array();
		while ($row = $r->FetchRow()) {
			$wp = PageWorkflowProgress::getByID($row['wpID']);
			if (is_object($wp)) {
				$list[] = $wp;
			}
		}
		return $list;
	}

	public function getWorkflowProgressFormAction() {
		return DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $this->cID . '&wpID=' . $this->getWorkflowProgressID() . '&ctask=workflow_progress&' . Loader::helper('validation/token')->getParameter();
	}
	
	public static function getMyPendingProgressObjects() {
		return array();
	}
	

}
