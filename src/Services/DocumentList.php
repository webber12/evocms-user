<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;
use EvolutionCMS\Models\SiteContent;



class DocumentList extends Service
{

    public function process($params = [])
    {
        $uid = $params['user'] ?? 0;
        $documents = $this->getUserDocuments($uid);
        return $this->makeResponse($documents);
    }

    protected function getUserDocuments($user)
    {
        $display = $this->getCfg("DocumentListDisplay", 15);
        $sortBy = $this->getCfg("DocumentListSortBy", 'menuindex');
        $sortDir = $this->getCfg("DocumentListSortDir", 'DESC');
        $onlyActive = $this->getCfg("DocumentListOnlyActive", false);
        $showUndeleted = $this->getCfg("DocumentListShowUndeleted", true);
        $fields = $this->getCfg("DocumentListFields", 'id,pagetitle');
        $columns = array_map('trim', explode(',', $fields));
        $tvs = $this->getCfg("DocumentListTVs", '');

        if(!empty($tvs)) {
            $tvs = array_map('trim', explode(',', $tvs));
            $columns = array_merge($columns, $tvs);
        }
        $res = SiteContent::where('createdby', $user)
            ->orderBy($sortBy, $sortDir);
        if(!empty($onlyActive)) {
            $res = $res->active();
        }
        if(!empty($showUndeleted)) {
            $res = $res->where('deleted', 0);
        }
        if(!empty($tvs)) {
            $res = $res->withTVs($tvs);
        }
        $res = $res->paginate($display)
            ->toArray();
        $arr = [];
        $tmp = $res['data'];
        foreach($tmp as $k => $row) {
            foreach($columns as $column) {
                if($row[$column] !== false) {
                    $arr[$k][$column] = $row[$column];
                }
            }
        }
        $res['data'] = $arr;
        return $res;
    }
}
