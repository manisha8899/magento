<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Smallworld\Quote\Model\Rewrite\Quote\Item;

class Repository extends \Magento\Quote\Model\Quote\Item\Repository
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Product repository.
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Quote\Api\Data\CartItemInterfaceFactory
     */
    protected $itemDataFactory;

    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $itemDataFactory,
        \SW\Tryus\Model\QuantityValidation $quantityValidation,
        \Magento\Framework\App\Config\ScopeConfigInterface $storeConfig,
        \Smallworld\Quote\Api\Data\CartdataInterfaceFactory $dataFactory,
        array $data = []
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->productRepository = $productRepository;
        $this->itemDataFactory = $itemDataFactory;
        $this->dataFactory = $dataFactory;
        $this->quantityValidation = $quantityValidation;
        $this->storeConfig = $storeConfig;
        parent::__construct($quoteRepository, $productRepository, $itemDataFactory, $data);
    }

    private function canAllowTryus($quote)
    {

        $is_enabled = $this->storeConfig->getValue('tryus/tryus/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $is_guest = $this->storeConfig->getValue('tryus/tryus/guest', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$is_enabled) {
            return;
        }
        $catids = array();
        $quote_items = $quote->getAllItems();
        foreach ($quote_items as $item) {
            if ($item->getHasChildren()) {
                continue;
            }
            
            $product = $this->productRepository->getById($item->getProductId());
            $categories = $product->getCategoryIds();
            $catids = array_merge($catids, $categories);
            $product = $item->getProduct();
            $timespan_sl = 'lifetime';
            $max_qty_allow = $product->getData('qty_limit_per_customer');
            if ($this->quantityValidation->isLimitedQty($product)) {
                $order_item_count = $this->quantityValidation->getOrderItemHistoryCount($item->getSku(), $timespan_sl);
                if ($order_item_count || $order_item_count >= 0) {
                    $this->quantityValidation->calculateMaxAllowedQty($max_qty_allow, $item->getTotalQty(), $order_item_count);
                }
            }
        }

        $categoryids = array_unique($catids);
        if (in_array(37, $categoryids)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Sorry! You are not allowed to add two sample products at same time'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
       
        $pre_added_product = 0;
        $pre_added_product2 = 0;
        $pre_added_product3 = 0;
        $pre_added_product4 = 0;
        $pre_added_product5 = 0;
        $pre_added_product6 = 0;
        $pre_added_product7 = 0;
        $pre_added_product8 = 0;
        $pre_added_product9 = 0;
        $pre_added_product10 = 0;
        $checkkittuff=0;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productRepository = $objectManager->get('\Magento\Catalog\Model\ProductRepository');
        $productObj = $productRepository->get($cartItem->getSku());
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $productSkuConfig = $scopeConfig->getValue('pwa/auto_add/auto_add_skus', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $productSkuArray= explode(",",$productSkuConfig);
        $couponConfig = $scopeConfig->getValue('pwa/auto_add/coupon_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $couponArray= explode(",",$couponConfig);
        //for second product coupon
        $productSecSkuConfig = $scopeConfig->getValue('pwa/autoadd_config/auto_add_sec_skus', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $productSecSkuArray= explode(",",$productSecSkuConfig);
        $couponSecConfig = $scopeConfig->getValue('pwa/autoadd_config/coupon_sec_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $couponSecArray= explode(",",$couponSecConfig);
        //for Third product coupon
        $productThrSkuConfig = $scopeConfig->getValue('pwa/auto_thr_add/auto_add_thr_skus', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $productThrSkuArray= explode(",",$productThrSkuConfig);
        $couponThrConfig = $scopeConfig->getValue('pwa/auto_thr_add/coupon_thr_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $couponThrArray= explode(",",$couponThrConfig);
        //for Fourth product coupon
        $productForSkuConfig = $scopeConfig->getValue('pwa/auto_for_add/auto_add_for_skus', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $productForSkuArray= explode(",",$productForSkuConfig);
        $couponForConfig = $scopeConfig->getValue('pwa/auto_for_add/coupon_for_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $couponForArray= explode(",",$couponForConfig);
        //for Fifth product coupon
        $productFifthSkuConfig = $scopeConfig->getValue('pwa/auto_fve_add/auto_add_fve_skus', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $productFifthSkuArray= explode(",",$productFifthSkuConfig);
        $couponFifthConfig = $scopeConfig->getValue('pwa/auto_fve_add/coupon_fve_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $couponFifthArray= explode(",",$couponFifthConfig);
        //for Sixth product coupon
        $productSixSkuConfig = $scopeConfig->getValue('pwa/auto_six_add/auto_add_six_skus', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $productSixSkuArray= explode(",",$productSixSkuConfig);
        $couponSixConfig = $scopeConfig->getValue('pwa/auto_six_add/coupon_six_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $couponSixArray= explode(",",$couponSixConfig);
        //for Seventh product coupon
        $productSevSkuConfig = $scopeConfig->getValue('pwa/auto_sev_add/auto_add_sev_skus', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $productSevSkuArray= explode(",",$productSevSkuConfig);
        $couponSevConfig = $scopeConfig->getValue('pwa/auto_sev_add/coupon_sev_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $couponSevArray= explode(",",$couponSevConfig);
        //for Eighth product coupon
        $productEigSkuConfig = $scopeConfig->getValue('pwa/auto_eig_add/auto_add_eig_skus', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $productEigSkuArray= explode(",",$productEigSkuConfig);
        $couponEigConfig = $scopeConfig->getValue('pwa/auto_eig_add/coupon_eig_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $couponEigArray= explode(",",$couponEigConfig);
        //for Ninth product coupon
        $productNinSkuConfig = $scopeConfig->getValue('pwa/auto_nin_add/auto_add_nin_skus', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $productNinSkuArray= explode(",",$productNinSkuConfig);
        $couponNinConfig = $scopeConfig->getValue('pwa/auto_nin_add/coupon_nin_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $couponNinArray= explode(",",$couponNinConfig);
        //for Tenth product coupon
        $productTenSkuConfig = $scopeConfig->getValue('pwa/auto_ten_add/auto_add_ten_skus', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $productTenSkuArray= explode(",",$productTenSkuConfig);
        $couponTenConfig = $scopeConfig->getValue('pwa/auto_ten_add/coupon_ten_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $couponTenArray= explode(",",$couponTenConfig);
        $cartStatus = $scopeConfig->getValue('pwa/auto_add/status_coupon', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if(in_array($cartItem->getSku(), $productSkuArray) &&  $cartItem->getQty() > 1 
        && in_array(101, $productObj->getCategoryIds()))	
        {
            throw new \Magento\Framework\Exception\LocalizedException(__('Sorry! You cannot buy more than one of with sample product'));
        }
        if(in_array($cartItem->getSku(), $productSecSkuArray) &&  $cartItem->getQty() > 1
        && in_array(101, $productObj->getCategoryIds()))	
        {
            throw new \Magento\Framework\Exception\LocalizedException(__('Sorry! You cannot buy more than one of with sample product'));
        }
        if(in_array($cartItem->getSku(), $productThrSkuArray) &&  $cartItem->getQty() > 1
        && in_array(101, $productObj->getCategoryIds()))	
        {
            throw new \Magento\Framework\Exception\LocalizedException(__('Sorry! You cannot buy more than one of with sample product'));
        }
        if(in_array($cartItem->getSku(), $productForSkuArray) &&  $cartItem->getQty() > 1
        && in_array(101, $productObj->getCategoryIds()))	
        {
            throw new \Magento\Framework\Exception\LocalizedException(__('Sorry! You cannot buy more than one of with sample product'));
        }
        if(in_array($cartItem->getSku(), $productFifthSkuArray) &&  $cartItem->getQty() > 1
        && in_array(101, $productObj->getCategoryIds()))	
        {
            throw new \Magento\Framework\Exception\LocalizedException(__('Sorry! You cannot buy more than one of with sample product'));
        }
        if(in_array($cartItem->getSku(), $productSixSkuArray) &&  $cartItem->getQty() > 1
        && in_array(101, $productObj->getCategoryIds()))	
        {
            throw new \Magento\Framework\Exception\LocalizedException(__('Sorry! You cannot buy more than one of with sample product'));
        }
        if(in_array($cartItem->getSku(), $productSevSkuArray) &&  $cartItem->getQty() > 1
        && in_array(101, $productObj->getCategoryIds()))	
        {
            throw new \Magento\Framework\Exception\LocalizedException(__('Sorry! You cannot buy more than one of with sample product'));
        }
        if(in_array($cartItem->getSku(), $productEigSkuArray) &&  $cartItem->getQty() > 1
        && in_array(101, $productObj->getCategoryIds()))	
        {
            throw new \Magento\Framework\Exception\LocalizedException(__('Sorry! You cannot buy more than one of with sample product'));
        }
        if(in_array($cartItem->getSku(), $productNinSkuArray) &&  $cartItem->getQty() > 1
        && in_array(101, $productObj->getCategoryIds()))	
        {
            throw new \Magento\Framework\Exception\LocalizedException(__('Sorry! You cannot buy more than one of with sample product'));
        }
        if(in_array($cartItem->getSku(), $productTenSkuArray) &&  $cartItem->getQty() > 1
        && in_array(101, $productObj->getCategoryIds()))	
        {
            throw new \Magento\Framework\Exception\LocalizedException(__('Sorry! You cannot buy more than one of with sample product'));
        }
        if ($productObj->getTypeId() == 'simple') {
            $StockState = $objectManager->get('\Magento\CatalogInventory\Api\StockStateInterface');
            $stockqty = $StockState->getStockQty($productObj->getId(), $productObj->getStore()->getWebsiteId());
            $reqqty = $cartItem->getQty();
            if ($reqqty > $stockqty) {
                throw new \Magento\Framework\Exception\LocalizedException(__
                    ("We don't have as many ".$productObj->getName()." you requested."));
            }
        }
        $quoteFactory = $objectManager->create('\Magento\Quote\Model\QuoteFactory');
        $quoteData    = $quoteFactory->create()->load($cartItem->getQuoteId());
        if($quoteData->getCustomerId() && in_array(37, $productObj->getCategoryIds()))
        {
            throw new \Magento\Framework\Exception\LocalizedException(__('You could not buy TUFF product only guest user can buy this product.'));
        }

        if(in_array(37, $productObj->getCategoryIds()) && $cartItem->getQty() > 1)
        {
            throw new \Magento\Framework\Exception\LocalizedException(__('You could not add more than 1 QTY of TUFF Product.'));
        }

        /** @var \Magento\Quote\Model\Quote $quote */
        $cartId = $cartItem->getQuoteId();
        $quote = $this->quoteRepository->getActive($cartId);
        // Check Product is gift or not and coupon has applied or not

        // Check Product condition removed 
        
        // if(in_array(7, $productObj->getCategoryIds()) && $quote->getCouponCode()!='')
        // {
        //     throw new \Magento\Framework\Exception\LocalizedException(__('Sorry! You cannot add gift product with coupon.'));
        // }


        // Check Other product buy with Free Product
        $checkOtherProductWithFree = 0;
        $checkFreeProductWithOther = 0;
        $quote_items = $quote->getAllItems();
        foreach ($quote_items as $item) {
            
            if($item->getPrice() > 0 && in_array(37, $productObj->getCategoryIds()))
            {
                $checkOtherProductWithFree = 1;
            }

            $itemProductObj = $productRepository->get($item->getSku());

            if(in_array(37, $itemProductObj->getCategoryIds()) && $productObj->getPrice() > 0)
            {
                $checkFreeProductWithOther = 1;
            }
            if(in_array($item->getSku(), $productSkuArray))
            {
                $pre_added_product+=1;
            }
            //for second product
            if(in_array($item->getSku(), $productSecSkuArray))
            {
                $pre_added_product2 +=1;
            }
            //for third product
            if(in_array($item->getSku(), $productThrSkuArray))
            {
                $pre_added_product3 +=1;
            }
             //for Fourth product
             if(in_array($item->getSku(), $productForSkuArray))
             {
                 $pre_added_product4 +=1;
             }
        }

        if($checkOtherProductWithFree)
        {
            throw new \Magento\Framework\Exception\LocalizedException(__('Sorry! You cannot buy sample product with this product.'));
        }

        if($checkFreeProductWithOther)
        {
            throw new \Magento\Framework\Exception\LocalizedException(__('Sorry! You cannot buy this product with sample product.'));
        }

        //Try us for free logic
        $this->canAllowTryus($quote);

        $quoteItems = $quote->getItems();
        $quoteItems[] = $cartItem;
        $quote->setItems($quoteItems);
        $this->quoteRepository->save($quote);

        $quote->collectTotals();
        if($pre_added_product >= 1 && !in_array(strtolower($quote->getCouponCode()),$couponArray))
        {
            if(!$cartStatus)
            {
                if(empty($quote->getCouponCode())){
                    $quote2 = $this->quoteRepository->getActive($cartId);
                    $items = $quote->getAllVisibleItems();
                    foreach ($items as $item) {
                        if(in_array($item->getSku(), $productSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponArray))
                        {
                            $itemid = $item->getItemId();
                            $quote2->removeItem($itemid)->save();
                        }
                    }
                    $this->quoteRepository->save($quote2->collectTotals());
                }
            }
        }
        //for second product
        if($pre_added_product2 >= 1 && !in_array(strtolower($quote->getCouponCode()),$couponSecArray))
        {
            if(empty($quote->getCouponCode())){
            $quote2 = $this->quoteRepository->getActive($cartId);
            $items = $quote->getAllVisibleItems();
            foreach ($items as $item) {
                if(in_array($item->getSku(), $productSecSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponSecArray))
                {
                    $itemid = $item->getItemId();
                    $quote2->removeItem($itemid)->save();
                }
            }
            $this->quoteRepository->save($quote2->collectTotals());
         }
        }
        //for third product
        if($pre_added_product3 >= 1 && !in_array(strtolower($quote->getCouponCode()),$couponThrArray))
        {
            if(empty($quote->getCouponCode())){
            $quote2 = $this->quoteRepository->getActive($cartId);
            $items = $quote->getAllVisibleItems();
            foreach ($items as $item) {
                if(in_array($item->getSku(), $productThrSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponThrArray))
                {
                    $itemid = $item->getItemId();
                    $quote2->removeItem($itemid)->save();
                }
            }
             $this->quoteRepository->save($quote2->collectTotals());
            }
        }
         //for Fourth product
         if($pre_added_product4 >= 1 && !in_array(strtolower($quote->getCouponCode()),$couponForArray))
         {
            if(empty($quote->getCouponCode())){
             $quote2 = $this->quoteRepository->getActive($cartId);
             $items = $quote->getAllVisibleItems();
             foreach ($items as $item) {
                 if(in_array($item->getSku(), $productForSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponForArray))
                 {
                     $itemid = $item->getItemId();
                     $quote2->removeItem($itemid)->save();
                 }
             }
             $this->quoteRepository->save($quote2->collectTotals());
            }
         }
          //for Fifth product
        if($pre_added_product5 >= 1 && !in_array(strtolower($quote->getCouponCode()),$couponFifthArray))
        {
            if(empty($quote->getCouponCode())){
            $quote2 = $this->quoteRepository->getActive($cartId);
            $items = $quote->getAllVisibleItems();
            foreach ($items as $item) {
                if(in_array($item->getSku(), $productFifthSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponFifthArray))
                {
                    $itemid = $item->getItemId();
                    $quote2->removeItem($itemid)->save();
                }
             }
              $this->quoteRepository->save($quote2->collectTotals());
           }
        }
        //for Sixth product
        if($pre_added_product6 >= 1 && !in_array(strtolower($quote->getCouponCode()),$couponSixArray))
        {
            if(empty($quote->getCouponCode())){
            $quote2 = $this->quoteRepository->getActive($cartId);
            $items = $quote->getAllVisibleItems();
            foreach ($items as $item) {
                if(in_array($item->getSku(), $productSixSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponSixArray))
                {
                    $itemid = $item->getItemId();
                    $quote2->removeItem($itemid)->save();
                }
            }
            $this->quoteRepository->save($quote2->collectTotals());
         }
        }
         //for Seventh product
         if($pre_added_product7 >= 1 && !in_array(strtolower($quote->getCouponCode()),$couponSevArray))
         {
            if(empty($quote->getCouponCode())){
             $quote2 = $this->quoteRepository->getActive($cartId);
             $items = $quote->getAllVisibleItems();
             foreach ($items as $item) {
                 if(in_array($item->getSku(), $productSevSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponSevArray))
                 {
                     $itemid = $item->getItemId();
                     $quote2->removeItem($itemid)->save();
                 }
             }
             $this->quoteRepository->save($quote2->collectTotals());
            }
         }
          //for Eight product
        if($pre_added_product8 >= 1 && !in_array(strtolower($quote->getCouponCode()),$couponEigArray))
        {
            if(empty($quote->getCouponCode())){
                $quote2 = $this->quoteRepository->getActive($cartId);
                $items = $quote->getAllVisibleItems();
                foreach ($items as $item) {
                    if(in_array($item->getSku(), $productEigSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponEigArray))
                    {
                        $itemid = $item->getItemId();
                        $quote2->removeItem($itemid)->save();
                    }
                }
                $this->quoteRepository->save($quote2->collectTotals());
            }
        }
        //for Ninth product
        if($pre_added_product9 >= 1 && !in_array(strtolower($quote->getCouponCode()),$couponNinArray))
        {
            if(empty($quote->getCouponCode())){
                $quote2 = $this->quoteRepository->getActive($cartId);
                $items = $quote->getAllVisibleItems();
                foreach ($items as $item) {
                    if(in_array($item->getSku(), $productNinSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponNinArray))
                    {
                        $itemid = $item->getItemId();
                        $quote2->removeItem($itemid)->save();
                    }
                }
                $this->quoteRepository->save($quote2->collectTotals());
            }
        }
         //for Tenth product
         if($pre_added_product10 >= 1 && !in_array(strtolower($quote->getCouponCode()),$couponTenArray))
         {
            if(empty($quote->getCouponCode())){
             $quote2 = $this->quoteRepository->getActive($cartId);
             $items = $quote->getAllVisibleItems();
             foreach ($items as $item) {
                 if(in_array($item->getSku(), $productTenSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponTenArray))
                 {
                     $itemid = $item->getItemId();
                     $quote2->removeItem($itemid)->save();
                 }
             }
             $this->quoteRepository->save($quote2->collectTotals());
            }
         }
      
        return $quote->getLastAddedItem();
    }
}
