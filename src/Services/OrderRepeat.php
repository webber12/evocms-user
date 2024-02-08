<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;
use EvolutionCMS\Models\SiteContent;

class OrderRepeat extends Service
{
    public function process($params = [])
    {
        $order_id = $params['id'] ?? 0;

        $errors = [];

        $currentUser = $this->getUser();

        evo()->invokeEvent('OnWebPageInit');//для инициализации commerce

        $processor = ci()->commerce->loadProcessor();
        $order = $processor->loadOrder($order_id, true);
        $items = $processor->getCart()->getItems();
        $active = SiteContent::select(['id'])->whereIn('id', array_column($items, 'id'))
            ->active()->get()->pluck('id')->toArray();

        $add = [];
        foreach($items as $item) {
            //можно добавить только активные (неудаленные опубликованные) товары
            if(in_array($item['id'], $active)) {
                $add[] = [
                    'id' => $item['id'],
                    'count' => $item['count'],
                    'name' => $item['name'],
                    'options' => $item['options'],
                    'meta' => $item['meta'],
                ];
            }
        }
        if(!empty($add)) {
            $cartName = $this->getCfg("OrderRepeatCartName", 'products');
            $cart = ci()->carts->getCart($cartName);
            $response = $cart->addMultiple($add);
        } else {
            $errors['common'][] = $this->trans('common_order_empty');
        }

        if (!empty($errors)) {
            $response = [ 'status' => 'error', 'errors' => $errors ];
        } else {
            $response = [ 'status' => 'ok', 'message' => $response ];
        }


        return $this->makeResponse($response);
    }
}
