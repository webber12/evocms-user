<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;

class OrderInfo extends Service
{

    public function process($params = [])
    {
        $order_id = $params['id'] ?? 0;

        evo()->invokeEvent('OnWebPageInit');//для инициализации commerce

        $processor = ci()->commerce->loadProcessor();
        $order = $processor->loadOrder($order_id, true);
        $items = $processor->getCart()->getItems();
        $query   = evo()->db->select('*', evo()->getFullTablename('commerce_order_history'), "`order_id` = '" . $order_id . "'", 'created_at DESC');
        $history = evo()->db->makeArray($query);

        $items_price = 0;
        foreach($items as $item) {
            $items_price += $item['price'] * $item['count'];
        }
        $order['items_price'] = $items_price;

        $subtotals = [];
        $total = $order['items_price'];
        evo()->invokeEvent('OnCollectSubtotals', [
            'rows'     => &$subtotals,
            'total'    => &$total,
            'realonly' => true,
        ]);
        $order['subtotals'] = $subtotals;

        return $this->makeResponse([ 'order' => $order, 'items' => $items, 'history' => $history ]);
    }
}
