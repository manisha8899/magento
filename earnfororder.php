<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Catalog\Api\ProductRepositoryInterface;

class EarnForOrder implements ObserverInterface
{
    /**
     * Reward place order restriction interface
     *
     * @var \Magento\Reward\Observer\PlaceOrder\RestrictionInterface
     */
    protected $_restriction;

    /**
     * Reward model factory
     *
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $_modelFactory;

    /**
     * Reward resource model factory
     *
     * @var \Magento\Reward\Model\ResourceModel\RewardFactory
     */
    protected $_resourceFactory;

    /**
     * Core model store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Reward helper.
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $rewardHelper;
	/**
		* @var \Magento\SalesRule\Api\RuleRepositoryInterface
		*/
		protected $ruleRepositoryInterface;
		protected $_logger;
    /**
     * @param \Magento\Reward\Observer\PlaceOrder\RestrictionInterface $restriction
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Reward\Model\RewardFactory $modelFactory
     * @param \Magento\Reward\Model\ResourceModel\RewardFactory $resourceFactory
     * @param \Magento\Reward\Helper\Data $rewardHelper
     */
    public function __construct(
        \Magento\Reward\Observer\PlaceOrder\RestrictionInterface $restriction,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Reward\Model\RewardFactory $modelFactory,
        \Magento\Reward\Model\ResourceModel\RewardFactory $resourceFactory,
        \Magento\Reward\Helper\Data $rewardHelper,
		\Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepositoryInterface,
		\Psr\Log\LoggerInterface $logger,
		ProductRepositoryInterface $productRepository
    ) {
        $this->_restriction = $restriction;
        $this->_storeManager = $storeManager;
        $this->_modelFactory = $modelFactory;
        $this->_resourceFactory = $resourceFactory;
        $this->rewardHelper = $rewardHelper;
		$this->_logger = $logger;
		$this->ruleRepositoryInterface = $ruleRepositoryInterface;
		$this->productRepository = $productRepository;
    }
    /**
     * send event to sqs
     * @param mixed $DataArray
     * @return void
     */
    public function sendEventToSqs($DataArray){
        $HelperObject = \Magento\Framework\App\ObjectManager::getInstance();
        $Helper = $HelperObject->create('Smallworld\Pwa\SqsHelper\Helper');
        $Helper->EventToSqs($DataArray);
    }

    /**
     * send event to sqs
     * @param mixed $DataArray
     * @return void
     */
    public function sendEventToOrderQueue($DataArray){
        $HelperObject = \Magento\Framework\App\ObjectManager::getInstance();
        $Helper = $HelperObject->create('Smallworld\Pwa\SqsHelper\Helper');
        $Helper->OrderEventSQS($DataArray);
    }
    /**
     * orderDeliveredEvent
     *
     * @param int $customerId , $orderTotal, $cashBack,$customerPhone,$orderId
     * @param string $customerName,$customerEmail
     * @return void
     */
    public function orderDeliveredEvent($customerId,$customerName,$customerPhone,$customerEmail,$orderId,$orderTotal,$cashBack, $entityId){
        $balance_factory_object = \Magento\Framework\App\ObjectManager::getInstance();
        $balance_factory = $balance_factory_object->get('\Magento\CustomerBalance\Model\BalanceFactory');
        $CustomerBalanceModel = $balance_factory->create()->setCustomerId($customerId)->loadByCustomer();  
        $DataArray = array("event_name" => "order_delivered_cashback_credit",
                            "customer_id"=>$customerId,
                            "customer_name"=>$customerName,
                            "customer_phone"=>"+91".$customerPhone,
                            "customer_email"=>$customerEmail,
                            "order_id"=>$orderId,
                            "order_total"=>number_format($orderTotal),
                            "cashback_amount"=>number_format($cashBack),
                            "updated_balance" => $CustomerBalanceModel->getAmount(),
                            "entity_id"=>$entityId
                        );
        $this->sendEventToSqs($DataArray);    
    }
    /**
     * orderprocessingEvent
     *
     * @param int $customerId , $orderTotal,$customerPhone,$orderId,$cashBack
     * @param string $customerName,$payment_method,$coupon,$shiping,$discount,$tax,$customerEmail
     * @return void
     */
    public function orderProcessingEvent($customerId,$customerName,$customerPhone,$customerEmail,$orderId,$orderTotal,$payment_method,$coupon,$discount,$tax,$shiping,$cashBack, $entityId){
        $DataArray = array("event_name" => "order_processing",
                            "customer_id"=>$customerId,
                            "customer_name"=>$customerName,
                            "customer_phone"=>"+91".$customerPhone,
                            "customer_email"=>$customerEmail,
                            "order_id"=>$orderId,
                            "order_total"=>number_format($orderTotal),
                            "payment_method"=>$payment_method,
                            "coupon_code" => $coupon,
                            "discount_amount"=>number_format($discount),
                            "tax_amount"  =>number_format($tax),
                            "shiping_amount"=>number_format($shiping),
                            "cashback_amount"=>number_format($cashBack),
                            "entity_id"=>$entityId
                        );
        $this->sendEventToOrderQueue(array('entity_id'=>$entityId,'orderId'=>$orderId,'event_name'=>'order_confirmed'));
        $this->sendEventToSqs($DataArray);
    }
    /**
     * orderDispatchEvent
     *
     * @param int $customerId , $orderTotal,$customerPhone,$orderId
     * @param string $customerName,$customerEmail
     * @return void
     */
    public function orderDispatchEvent($customerId,$customerName,$customerPhone,$customerEmail,$orderId,$orderTotal, $entityId){
        $DataArray = array("event_name" => "in_transit",
                            "customer_id"=>$customerId,
                            "customer_name"=>$customerName,
                            "customer_phone"=>"+91".$customerPhone,
                            "customer_email"=>$customerEmail,
                            "order_id"=>$orderId,
                            "order_total"=>number_format($orderTotal),
                            "entity_id"=>$entityId
                        );
        $this->sendEventToSqs($DataArray);    
    }
   /**
     * orderRtoEvent
     *
     * @param int $customerId , $orderTotal, $cashBack,$customerPhone,$orderId
     * @param string $customerName
     * @return void
     */
    public function orderRtoEvent($customerId,$customerName,$customerPhone,$customerEmail,$orderId,$orderTotal,$cashBack, $entityId){
        $DataArray = array("event_name" => "order_rto_cashback_revert",
                            "customer_id"=>$customerId,
                            "customer_name"=>$customerName,
                            "customer_phone"=>"+91".$customerPhone,
                            "customer_email"=>$customerEmail,
                            "order_id"=>$orderId,
                            "order_total"=>number_format($orderTotal),
                            "cashback_amount"=>number_format($cashBack),
                            "entity_id"=>$entityId
                        );
        $this->sendEventToSqs($DataArray);    
    }
    
       /**
     * orderRtoNoCashbackEvent
     *
     * @param int $customerId , $orderTotal, $cashBack,$customerPhone,$orderId
     * @param string $customerName
     * @return void
     */
    public function orderRtoNoCashbackEvent($customerId,$customerName,$customerPhone,$customerEmail,$orderId,$orderTotal,$cashBack, $entityId){
        $DataArray = array("event_name" => "order_rto",
                            "customer_id"=>$customerId,
                            "customer_name"=>$customerName,
                            "customer_phone"=>"+91".$customerPhone,
                            "customer_email"=>$customerEmail,
                            "order_id"=>$orderId,
                            "order_total"=>number_format($orderTotal),
                            "cashback_amount"=>number_format($cashBack),
                            "entity_id"=>$entityId
                        );
        $this->sendEventToSqs($DataArray);    
    }

    /**
     * Increase reward points balance for sales rules applied to order.
     *
     * @param Observer $observer
     * @return int $pointsDelta
     */

    public function cashbackcaldb(Observer $observer) {
        $pointsDelta = 0;
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();
        $incrementId =  $order->getIncrementId();
        // Check if FREE Product exists in current order
		$orderItems    = $order->getAllItems();
        $hasFreeItem   = 0;
        $kit_product_totals=0;
        $kit_product=0;
		foreach ($orderItems as $item) {
			$product  = $this->productRepository->getById($item->getProductId());
			$proPrice = $product->getPrice();
			if(in_array(37, $product->getCategoryIds())) {
		    	$hasFreeItem = 1;
			}
			if(in_array(7, $product->getCategoryIds())){
                $kit_product=1;
				$kit_product_totals += $item->getBaseRowTotalInclTax();
			}
		}
        $appliedRuleIds = array_unique(explode(',', $order->getAppliedRuleIds()));

        /** @var $resource \Magento\Reward\Model\ResourceModel\Reward */
        $checkChangeCondition = 0;
        // Check product is free or not and fee has charged or not
        if($order->getFee() < 0.0001 && $hasFreeItem)
        {
        	$checkChangeCondition = 1;
        }
        $rewardRules = $this->_resourceFactory->create()->getRewardSalesrule($appliedRuleIds);
        $order_grand_total=$order->getGrandTotal();
        if ($order->getPayment()->getMethod()=='msp_cashondelivery'){
            $cod = $order->getMspCodAmount();
            if(($order_grand_total - $cod)>=399 ){
                $order_grand_total-=$cod;
            }
        }
        $ordersub = $order_grand_total;
        $ordersub = floor(($ordersub*100))/100;
        foreach ($rewardRules as $rule) {
			$ruledtl = $this->ruleRepositoryInterface->getById($rule['rule_id']);
			$type=$ruledtl->getSimpleAction();
			if($type =='by_percent'){
				$pointsDelta += ( $rule['points_delta'] / 100) * $ordersub;		
			}else{
				$pointsDelta += (int)$rule['points_delta'];
            }
            if( ($rule['rule_id']!= '458937' ||  $rule['rule_id']!='458819' || $rule['rule_id'] != '458748') && $kit_product ==1 && $type =='by_percent'){
		if ($order->getPayment()->getMethod()!='msp_cashondelivery'){
                    $kit_product_totals -=$kit_product_totals *(5/100);
                    $kit_product_totals = floor(($kit_product_totals*100))/100;
                }
                $ordersub_without_kit = $ordersub - $kit_product_totals;
                $pointsDelta = ( $rule['points_delta'] / 100) * $ordersub_without_kit;
            }
			if($pointsDelta > 200 && ($rule['rule_id']== '458937' ||  $rule['rule_id']=='458819' || $rule['rule_id']=='459241') ){
                $pointsDelta=200;
            }
			if($pointsDelta > 100 && $rule['rule_id'] == '458748'){
				$pointsDelta=100;
            }
        }
        // 100% cashback for tuff product
        if($order->getFee() > 0 && $hasFreeItem){
        	$pointsDelta = $order->getFee();
        }
        return $pointsDelta;
    }
    /**
     * Increase reward points balance for sales rules applied to order.
     *
     * @param Observer $observer
     * @return void
    */
    public function execute(Observer $observer){
        if ($this->_restriction->isAllowed() === false) {
            return;
        }
        $pointsDelta = 0;
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();
        $incrementId =  $order->getIncrementId();
        // Check if FREE Product exists in current order
		$orderItems    = $order->getAllItems();
        $hasFreeItem   = 0;
        $kit_product_totals=0;
        $kit_product=0;
		foreach ($orderItems as $item) {
			$product  = $this->productRepository->getById($item->getProductId());
			$proPrice = $product->getPrice();
			if(in_array(37, $product->getCategoryIds())) {
		    	$hasFreeItem = 1;
            }	
            if(in_array(7, $product->getCategoryIds())){
                $kit_product=1;
				$kit_product_totals += $item->getBaseRowTotalInclTax();
			}
		}
		$status=$order->getStatus();
        $appliedRuleIds = array_unique(explode(',', $order->getAppliedRuleIds()));

        /** @var $resource \Magento\Reward\Model\ResourceModel\Reward */
        $checkChangeCondition = 0;
        // Check product is free or not and fee has charged or not
        if($order->getFee() < 0.0001 && $hasFreeItem){
        	$checkChangeCondition = 1;
        }
        $rewardRules = $this->_resourceFactory->create()->getRewardSalesrule($appliedRuleIds);
        $ordersub=$order->getGrandTotal();
        foreach ($rewardRules as $rule) {
			$ruledtl = $this->ruleRepositoryInterface->getById($rule['rule_id']);
			$type=$ruledtl->getSimpleAction();
			if($type =='by_percent'){
				$pointsDelta += ( $rule['points_delta'] / 100) * $ordersub;
				
			}else{
				$pointsDelta += (int)$rule['points_delta'];
            }
            if( ($rule['rule_id']!= '458937' ||  $rule['rule_id']!='458819' || $rule['rule_id'] != '458748') && $kit_product ==1 && $type =='by_percent'){
                
                if ($order->getPayment()->getMethod()!='msp_cashondelivery'){
                    $kit_product_totals -=$kit_product_totals *(5/100);
                    $kit_product_totals = floor(($kit_product_totals*100))/100;
                }
                $ordersub_without_kit = $ordersub - $kit_product_totals;
                $pointsDelta = ( $rule['points_delta'] / 100) * $ordersub_without_kit;

            }
			if($pointsDelta > 200 && ($rule['rule_id']== '458937' ||  $rule['rule_id']=='458819' || $rule['rule_id']=='459241')){
                $pointsDelta=200;
            }
			if($pointsDelta > 100 && $rule['rule_id'] == '458748'){
				$pointsDelta=100;
			}
        }
        // 100% cashback for tuff product
        if($order->getFee() > 0 && $hasFreeItem){
        	$pointsDelta = $order->getFee();
        }
        
        if($status == 'order_dispatched' || $status =='in_transit'){ 
            $this->orderDispatchEvent($order->getCustomerId(), $order->getShippingAddress()->getFirstname(),$order->getShippingAddress()->getTelephone(),$order->getBillingAddress()->getEmail(),$order->getIncrementId(),$order->getGrandTotal(),$pointsDelta, $order->getEntityId() );
        }
        if($status == 'processing'){
		$pointsDeltadb=0;
            try{
                $orderid =  $order->getIncrementId();
                $Order_exists=0;
                // check order already exists or not in database 
                $query_Object = \Magento\Framework\App\ObjectManager::getInstance();
                $temp_query = $query_Object->create('CashBack\AddCashBack\Model\CashBackAdd')->getCollection()
                                            ->addFieldToFilter('order_id',$orderid);
                $Order_exists = $temp_query->getSize();
                if($Order_exists ==0){
                    $pointsDeltadb = $this->cashbackcaldb($observer);
                    $query_Object = \Magento\Framework\App\ObjectManager::getInstance();
                    $temp_query = $query_Object->create('CashBack\AddCashBack\Model\CashBackAdd');
                    $temp_query->setOrderId($orderid);
                    $temp_query->setCashBack($pointsDeltadb);
                    $temp_query->setCashBackStatus(0);
                    $temp_query->save();
                }
            }catch (Exception $e) {
                throw $e->getMessage();
            }
            $this->orderProcessingEvent($order->getCustomerId(),$order->getShippingAddress()->getFirstname(),$order->getShippingAddress()->getTelephone(),$order->getBillingAddress()->getEmail(),$order->getIncrementId(),$order->getGrandTotal(),$order->getPayment()->getMethod(),$order->getCouponCode(),$order->getBaseDiscountAmount(),$order->getTaxAmount(),$order->getShippingAmount(),$pointsDeltadb, $order->getEntityId());
        }
	       if($status=='order_delivered' && $checkChangeCondition == 0){
            if ($pointsDelta && !$order->getCustomerIsGuest()) {
                $additionalInfo = __('Credit Against #%1',$order->getIncrementId());
                $reward = $this->_modelFactory->create();
                $reward->setCustomerId(
                    $order->getCustomerId()
                )->setWebsiteId(
                    $this->_storeManager->getStore($order->getStoreId())->getWebsiteId()
                )->setPointsDelta(
                    $pointsDelta
                )->setAction(
                    \Magento\Reward\Model\Reward::REWARD_ACTION_SALESRULE
                )->setActionEntity(
                    $order
                )->updateRewardPoints($additionalInfo);
                $order->addStatusHistoryComment(
                    __(
                        'Earned extra %1 #%2',
                        $this->rewardHelper->formatReward($pointsDelta),
                        $order->getIncrementId()
                    )
                );
                $query_Object = \Magento\Framework\App\ObjectManager::getInstance();
                    $temp_query = $query_Object->create('CashBack\AddCashBack\Model\CashBackAdd')->getCollection()->addFieldToFilter('order_id',$order->getIncrementId());
                    foreach($temp_query as $item){
                        $item->setCashBackStatus(1);
                        $item->save();
                    }
                $this->orderDeliveredEvent($order->getCustomerId(), $order->getShippingAddress()->getFirstname(),$order->getShippingAddress()->getTelephone(),$order->getBillingAddress()->getEmail(),$order->getIncrementId(),$order->getGrandTotal(),$pointsDelta, $order->getEntityId() );
            }
        }else if($status=='rto' || $status == 'rma'){
            $check = 0;
            $query_Object = \Magento\Framework\App\ObjectManager::getInstance();
            $temp_query = $query_Object->create('CashBack\AddCashBack\Model\CashBackAdd')->getCollection()
                                        ->addFieldToFilter('order_id',$order->getIncrementId());
            if($temp_query->getSize()){
                foreach($temp_query as $item){
                    $check = $item->getData('status');
                }
            }
            $revertStoreOrder = \Magento\Framework\App\ObjectManager::getInstance();
            $revertOrder = $revertStoreOrder->create('\Magento\CustomerBalance\Observer\RevertStoreCreditForOrder');
            $revertOrder->execute($order);
            
            if ($pointsDelta && !$order->getCustomerIsGuest() && $check == 1) {
                $additionalInfo = __('Debit Against  #%1',$order->getIncrementId());
                $reward = $this->_modelFactory->create();
                $reward->setCustomerId(
                    $order->getCustomerId()
                )->setWebsiteId(
                    $this->_storeManager->getStore($order->getStoreId())->getWebsiteId()
                )->setPointsDelta(
                    -$pointsDelta
                )->setAction(
                    \Magento\Reward\Model\Reward::REWARD_ACTION_REVERT
                )->setActionEntity(
                    $order
                )->updateRewardPoints($additionalInfo);
                $order->addStatusHistoryComment(
                    __(
                        'Refund %1 #%2 ',
                        $this->rewardHelper->formatReward($pointsDelta),
                        $order->getIncrementId()
                    )
                );
                $query_Object = \Magento\Framework\App\ObjectManager::getInstance();
                $temp_query = $query_Object->create('CashBack\AddCashBack\Model\CashBackAdd')->getCollection()->addFieldToFilter('order_id',$order->getIncrementId());
                foreach($temp_query as $item){
                    $item->setCashBackStatus(-1);
                    $item->save();
                }
                $this->orderRtoEvent($order->getCustomerId(), $order->getShippingAddress()->getFirstname(),$order->getShippingAddress()->getTelephone(), $order->getBillingAddress()->getEmail(),$order->getIncrementId(),$order->getGrandTotal(),$pointsDelta , $order->getEntityId());
            }
            $this->orderRtoNoCashbackEvent($order->getCustomerId(), $order->getShippingAddress()->getFirstname(),$order->getShippingAddress()->getTelephone(), $order->getBillingAddress()->getEmail(),$order->getIncrementId(),$order->getGrandTotal(),$pointsDelta,  $order->getEntityId());
        }
    }
}
