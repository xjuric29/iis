<?php

namespace App\Model;

use Nette;
use Tracy\Debugger;


class ViewUsers extends ListModel {
    private $sysuser;

    public function __construct(Nette\Database\Context $database, User $user) {
        $this->database = $database;
        $this->sysuser = $user;
        $this->paginator = new Nette\Utils\Paginator;
    }

    private function compare_role($a, $b) {
        return strnatcmp($a['role'], $b['role']);
    }

    private function compare_surname($a, $b) {
        return strnatcmp($a['surname'], $b['surname']);
    }

    private function compare_supervisor($a, $b) {
        return strnatcmp($a['superior'], $b['superior']);
    }

    private function compare_company($a, $b) {
        return strnatcmp($a['company'], $b['company']);
    }

    public function getUsersTable($orderBy, $page, $search, $userid, $orderDir) {
        $table = $this->getTable(null, null, $page, $search, $userid)->fetchAll();
        $tableArray = array();
        foreach ($table as $row) {
            $lastRow = $row->toArray();
            $lastRow['role'] = "customer";
            $lastRow['superior'] = "";
            $lastRow['company'] = "";
            if($this->sysuser->getUserType($lastRow['id']) == "worker") {
                $lastRow['role'] = $this->sysuser->getAdditionalUserData($lastRow['id'])->role;
                $superiorLogin = $this->sysuser->getAdditionalUserData($lastRow['id'])->superior;
                if($superiorLogin) {
                    $lastRow['superior'] = $this->database->table('user')
                        ->where('id LIKE ?', $superiorLogin)
                        ->fetch()
                        ->first_name;
                    $lastRow['superior'] .= " " . $this->database->table('user')
                            ->where('id LIKE ?', $superiorLogin)
                            ->fetch()
                            ->surname;
                }
            }
            else {
                $lastRow['company'] = $this->sysuser->getAdditionalUserData($lastRow['id'])->company;
            }
            $tableArray[] = $lastRow;
        }

        // Sorting the array instead of ordering in database
        switch($orderBy) {
            case "comp":
                usort($tableArray, array($this, 'compare_company'));
                break;
            case "role":
                usort($tableArray, array($this, 'compare_role'));
                break;
            case "svisor":
                usort($tableArray, array($this, 'compare_supervisor'));
                break;
            case "sname":
            default:
                usort($tableArray, array($this, 'compare_surname'));
        }

        if($orderDir == "desc") {
            array_reverse($tableArray);
        }
        Debugger::barDump(count($tableArray));
        $this->paginator->setItemsPerPage(10); // počet položek na stránce
        $this->paginator->setPage($page);
        $this->paginator->setItemCount(count($tableArray));
        return array_slice($tableArray, $this->paginator->getOffset(), $this->paginator->getLength());
    }

    protected function selectSearched($search, Nette\Database\Table\Selection $table) {
        if ($search) {
            $search = '%' . $search . '%';
            return $table->where("CONCAT(first_name, ' ',surname) LIKE ? OR first_name LIKE ? OR surname LIKE ? OR mail LIKE ?", $search, $search, $search, $search);
        }
        return $table;
    }

    protected function openTable($orderStr) {
        return $this->database->table('user');
    }

    protected function createOrderStr($orderBy)
    {
        return "surname";
    }

    protected function filterByUser($userid, Nette\Database\Table\Selection $table) {
        return $table->where("author LIKE ?", $userid);
    }

}
