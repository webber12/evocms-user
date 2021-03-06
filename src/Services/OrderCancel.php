<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;

class OrderCancel extends Service
{
    public function process($params = [])
    {
        $order_id = $params['id'] ?? 0;

        $currentUser = $this->getUser();

        $orderCancelStatus = $this->getCfg("OrderCancelStatus", 5);

        $errors = [];
        evo()->invokeEvent('OnWebPageInit');//для инициализации commerce
        $processor = ci()->commerce->loadProcessor();
        $order = $processor->loadOrder($order_id, true);
        if (in_array($order['status_id'], $this->getCfg("OrderCancelAvailableStatuses", []))) {
            //$customErrors = $this->makeCustomValidator($data);
            if (!empty($customErrors)) {
                $errors['customErrors'] = $customErrors;
            } else {
                $comment = 'canceled by evocms-user ' . $currentUser['username'] . ' (' . $currentUser['id'] . ')';
                $processor->addOrderHistory($order_id, $orderCancelStatus, $comment);
            }
        } else if ($order['status_id'] == $orderCancelStatus ) {
            $errors['common'][] = 'order is already canceled';
        } else {
            $errors['common'][] = 'anavailable status to cancel';
        }
        if (!empty($errors)) {
            $response = [ 'status' => 'error', 'errors' => $errors ];
        } else {
            $response = [ 'status' => 'ok', 'message' => 'success order canceled' ];
        }

        return $this->makeResponse($response);
    }

}
