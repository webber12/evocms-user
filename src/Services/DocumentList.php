<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Helpers\Filters;
use EvolutionCMS\EvoUser\Services\Service;
use EvolutionCMS\Models\SiteContent;


class DocumentList extends Service
{

    public function process($params = [])
    {
        $documents = $this->getDocuments();
        return $this->makeResponse($documents);
    }

    protected function getDocuments()
    {
        $display = $this->getCfg("DocumentListDisplay", 15);
        $sortBy = $this->getCfg("DocumentListSortBy", 'menuindex');
        $sortDir = $this->getCfg("DocumentListSortDir", 'DESC');
        $onlyActive = $this->getCfg("DocumentListOnlyActive", false);
        $showUndeleted = $this->getCfg("DocumentListShowUndeleted", true);
        $fields = $this->getCfg("DocumentListFields", 'id,pagetitle');
        $columns = array_map('trim', explode(',', $fields));
        $tvs = $this->getCfg("DocumentListTvs", '');

        if(!empty($tvs)) {
            $tvs = array_map('trim', explode(',', $tvs));
            $columns = array_merge($columns, $tvs);
        }
        $res = SiteContent::query()
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
        $filters = $this->getCfg("DocumentListFilters", [] );
        //print_r($filters);die();
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

