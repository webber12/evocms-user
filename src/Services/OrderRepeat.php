<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;

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

        $add = [];
        foreach($items as $item) {
            $add[] = [
                'id' => $item['id'],
                'count' => $item['count'],
                'name' => $item['name'],
                'options' => $item['options'],
                'meta' => $item['meta'],
            ];
        }
        if(!empty($add)) {
            $cart = ci()->carts->getCart('products');
            $response = $cart->addMultiple($add);
        } else {
            $errors['common'][] = 'empty order';
        }

        if (!empty($errors)) {
            $response = [ 'status' => 'error', 'errors' => $errors ];
        } else {
            $response = [ 'status' => 'ok', 'message' => $response ];
        }


        return $this->makeResponse($response);
    }
}
