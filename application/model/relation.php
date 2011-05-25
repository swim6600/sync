<?php
class relation extends ADODB_Active_Record {

	public function is_connected($id, $network) {
		$rs = $this->Find("user_id = ? and network = ?", array($id, $network));
		if(!empty($rs) && $rs[0] instanceof relation) {
			return true;
		}else {
			return false;
		}
	}

	public function getRelation($id, $network) {
		$rs = $this->Find("user_id = ? and network = ?", array($id, $network));
		if(!empty($rs) && $rs[0] instanceof relation) {
			return $rs[0];
		}else {
			return false;
		}
	}
	
	public function getConnectedNum() {
		$rs = $this->Find("user_id = ?", array($this->user_id));
		if(!empty($rs)) {
			return count($rs);
		}else {
			return 0;
		}
	}

	public function getRelations($id) {
		$rs = $this->Find("user_id = ?", array($id));
		if(!empty($rs)) {
			return $rs;
		}else {
			return false;
		}
	}

	public function update($record) {
		if(!empty($this->id)) {
			$db = $this->DB();
			$db->AutoExecute("relations", $record, "UPDATE", "id = " . $this->id);
		}
	}
}