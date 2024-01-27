<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;

class OrderCancel extends Service
{
    public function process($params = [])
    {
        $errors = [];
        $order_id = $params['id'] ?? 0;
        $currentUser = $this->getUser();
        $orderCancelStatus = $this->getCfg("OrderCancelStatus", 5);

        evo()->invokeEvent('OnWebPageInit'); //для инициализации commerce
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
        } else if ($order['status_id'] == $orderCancelStatus) {
            $errors['common'][] = trans('evocms-user-core::messages.common_order_cancelled_already');
        } else {
            $errors['common'][] = trans('evocms-user-core::messages.common_order_status_na');
        }
        if (!empty($errors)) {
            $response = ['status' => 'error', 'errors' => $errors];
        } else {
            $response = ['status' => 'ok', 'message' => trans('evocms-user-core::messages.message_order_cancelled')];
        }

        return $this->makeResponse($response);
    }
}
