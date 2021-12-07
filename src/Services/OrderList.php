<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;
use \EvolutionCMS\UserManager\Services\UserManager;


class OrderList extends Service
{

    public function process($params = [])
    {
        $arr = [];

        $addWhereList = [];

        $DLParams = [
            'controller'      => 'onetable',
            'table'           => 'commerce_orders',
            'idType'          => 'documents',
            'id'              => 'list',
            'showParent'      => '-1',
            'api'             => 1,
            'ignoreEmpty'     => 1,
            'display'         => $this->getCfg('OrderListDisplay', 15),
            'paginate'        => 'pages',
        ];

        $DLParams['prepare'][] = function($data, $modx, $DL, $eDL) use ($fields, &$index) {
            $data['fields']    = json_decode($data['fields'], true);
            $data['index']     = $index;
            $data['iteration'] = ++$index;
            return $data;
        };

        $customPrepare = $this->getCfg("OrderListPrepare", false);
        if(!empty($customPrepare)) {
            $DLParams['prepare'][] = $customPrepare;
        }

        if(!empty($params['user'])) {
            $addWhereList[] = ' customer_id=' . $params['user'] . ' ';
        }
        if(!empty($addWhereList)) {
            $DLParams['addWhereList'] = implode(' AND ', $addWhereList);
        }

        $list = evo()->runSnippet('DocLister', $DLParams);

        $arr["from"] = evo()->getPlaceholder('list.from');
        $arr["to"] = evo()->getPlaceholder('list.to');
        $arr["current_page"] = evo()->getPlaceholder('list.current');
        $arr["last_page"] = evo()->getPlaceholder('list.totalPages');
        $arr["per_page"] = $this->getCfg('OrderListDisplay', 2);
        $arr["total"] = evo()->getPlaceholder('list.count');

        $arr['data'] = json_decode($list, true);

        return $this->makeResponse($arr);
    }
}
