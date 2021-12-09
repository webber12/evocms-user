<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Helpers\Filters;
use EvolutionCMS\EvoUser\Services\Service;
use EvolutionCMS\Models\SiteContent;


class DocumentListUser extends Service
{

    public function process($params = [])
    {
        $uid = $params['user'] ?? 0;
        $documents = $this->getUserDocuments($uid);
        return $this->makeResponse($documents);
    }

    protected function getUserDocuments($user)
    {
        $display = $this->getCfg("DocumentListUserDisplay", 15);
        $sortBy = $this->getCfg("DocumentListUserSortBy", 'menuindex');
        $sortDir = $this->getCfg("DocumentListUserSortDir", 'DESC');
        $onlyActive = $this->getCfg("DocumentListUserOnlyActive", false);
        $showUndeleted = $this->getCfg("DocumentListUserShowUndeleted", true);
        $fields = $this->getCfg("DocumentListUserFields", 'id,pagetitle');
        $columns = array_map('trim', explode(',', $fields));
        $tvs = $this->getCfg("DocumentListUserTvs", '');

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

        $filters = $this->getCfg("DocumentListUserFilters", [] );
        $res = (new Filters( $filters ))->injectFilters($res);

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
