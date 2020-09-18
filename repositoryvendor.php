<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Quote\Model\Quote\Item;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class Repository implements \Magento\Quote\Api\CartItemRepositoryInterface
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

    /**
     * @var CartItemProcessorInterface[]
     */
    protected $cartItemProcessors;

    /**
     * @var CartItemOptionsProcessor
     */
    private $cartItemOptionsProcessor;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Quote\Api\Data\CartItemInterfaceFactory $itemDataFactory
     * @param CartItemProcessorInterface[] $cartItemProcessors
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $itemDataFactory,
        array $cartItemProcessors = []
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->productRepository = $productRepository;
        $this->itemDataFactory = $itemDataFactory;
        $this->cartItemProcessors = $cartItemProcessors;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($cartId)
    {
        $output = [];
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        /** @var  \Magento\Quote\Model\Quote\Item  $item */
        foreach ($quote->getAllVisibleItems() as $item) {
            $item = $this->getCartItemOptionsProcessor()->addProductOptions($item->getProductType(), $item);
            $output[] = $this->getCartItemOptionsProcessor()->applyCustomOptions($item);
        }
        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $cartId = $cartItem->getQuoteId();
        $quote = $this->quoteRepository->getActive($cartId);
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
        $quoteItems = $quote->getItems();
        foreach ($quote_items as $item){
            if(in_array($item->getSku(), $productSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponArray))
            { 
                if(empty($quote->getCouponCode())){
                $itemid = $item->getItemId();
                $quote->removeItem($itemid)->save();
                }
            }
            if(in_array($item->getSku(), $productSecSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponSecArray))
            {
                if(empty($quote->getCouponCode())){
                $itemid = $item->getItemId();
                $quote->removeItem($itemid)->save();
                }
            } 
            if(in_array($item->getSku(), $productThrSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponThrArray))
            {
                if(empty($quote->getCouponCode())){
                $itemid = $item->getItemId();
                $quote->removeItem($itemid)->save();
                }
            }
            if(in_array($item->getSku(), $productForSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponForArray))
            {
                if(empty($quote->getCouponCode())){
                $itemid = $item->getItemId();
                $quote->removeItem($itemid)->save();
                }
            }
            if(in_array($item->getSku(), $productFifthSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponFifthArray))
            { 
                if(empty($quote->getCouponCode())){
                $itemid = $item->getItemId();
                $quote->removeItem($itemid)->save();
                }
            }
            if(in_array($item->getSku(), $productSixSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponSixArray))
            {
                if(empty($quote->getCouponCode())){
                $itemid = $item->getItemId();
                $quote->removeItem($itemid)->save();
                }
            } 
            if(in_array($item->getSku(), $productSevSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponSevArray))
            {
                if(empty($quote->getCouponCode())){
                $itemid = $item->getItemId();
                $quote->removeItem($itemid)->save();
                }
            }
            if(in_array($item->getSku(), $productEigSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponEigArray))
            {
                if(empty($quote->getCouponCode())){
                $itemid = $item->getItemId();
                $quote->removeItem($itemid)->save();
                }
            }
            if(in_array($item->getSku(), $productNinSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponNinArray))
            { 
                if(empty($quote->getCouponCode())){
                $itemid = $item->getItemId();
                $quote->removeItem($itemid)->save();
                }
            }
            if(in_array($item->getSku(), $productTenSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponTenArray))
            {
                if(empty($quote->getCouponCode())){
                $itemid = $item->getItemId();
                $quote->removeItem($itemid)->save();
                }
            } 
        }
        $this->quoteRepository->save($quote);
        $quoteItems[] = $cartItem;
        $quote->setItems($quoteItems);
        $this->quoteRepository->save($quote);
        $quote->collectTotals();
        return $quote->getLastAddedItem();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($cartId, $itemId)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $quoteItem = $quote->getItemById($itemId);
        if (!$quoteItem) {
            throw new NoSuchEntityException(
                __('Cart %1 doesn\'t contain item  %2', $cartId, $itemId)
            );
        }
        try {
            $quote->removeItem($itemId);
            $this->quoteRepository->save($quote);
            $quote_items = $quote->getAllVisibleItems();
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
            $quoteItems = $quote->getItems();
            foreach ($quote_items as $item){
                if(!in_array($item->getSku(), $productSkuArray) && in_array(strtolower($quote->getCouponCode()),$couponArray))
                {  
                    $quote->setCouponCode('');
                    $this->quoteRepository->save($quote->collectTotals());
                }
                if(in_array($item->getSku(), $productSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponArray))
                {
                    if(!$cartStatus)
                    {
                        if(empty($quote->getCouponCode())){
                        $itemid = $item->getItemId();
                        $quote->removeItem($itemid)->save();
                        }
                    }
                }
                if(in_array($item->getSku(), $productSecSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponSecArray))
                {
                    if(empty($quote->getCouponCode())){
                    $itemid = $item->getItemId();
                    $quote->removeItem($itemid)->save();
                    }
                }
                if(in_array($item->getSku(), $productThrSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponThrArray))
                {
                    if(empty($quote->getCouponCode())){
                    $itemid = $item->getItemId();
                    $quote->removeItem($itemid)->save();
                  }
                }
                if(in_array($item->getSku(), $productForSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponForArray))
                {
                    if(empty($quote->getCouponCode())){
                    $itemid = $item->getItemId();
                    $quote->removeItem($itemid)->save();
                 }
                }
                if(in_array($item->getSku(), $productFifthSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponFifthArray))
                {
                    if(empty($quote->getCouponCode())){
                    $itemid = $item->getItemId();
                    $quote->removeItem($itemid)->save();
                    }
                }
                if(in_array($item->getSku(), $productSixSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponSixArray))
                {
                    if(empty($quote->getCouponCode())){
                    $itemid = $item->getItemId();
                    $quote->removeItem($itemid)->save();
                  }
                }
                if(in_array($item->getSku(), $productSevSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponSevArray))
                {
                    if(empty($quote->getCouponCode())){
                    $itemid = $item->getItemId();
                    $quote->removeItem($itemid)->save();
                 }
                }
                if(in_array($item->getSku(), $productEigSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponEigArray))
                {
                    if(empty($quote->getCouponCode())){
                    $itemid = $item->getItemId();
                    $quote->removeItem($itemid)->save();
                  }
                }
                if(in_array($item->getSku(), $productNinSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponNinArray))
                {
                    if(empty($quote->getCouponCode())){
                    $itemid = $item->getItemId();
                    $quote->removeItem($itemid)->save();
                 }
                }
                if(in_array($item->getSku(), $productTenSkuArray) && !in_array(strtolower($quote->getCouponCode()),$couponTenArray))
                {
                    if(empty($quote->getCouponCode())){
                    $itemid = $item->getItemId();
                    $quote->removeItem($itemid)->save();
                    }
                }
            }
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not remove item from quote'));
        }
        return true;
    }

    /**
     * @return CartItemOptionsProcessor
     * @deprecated
     */
    private function getCartItemOptionsProcessor()
    {
        if (!$this->cartItemOptionsProcessor instanceof CartItemOptionsProcessor) {
            $this->cartItemOptionsProcessor = ObjectManager::getInstance()->get(CartItemOptionsProcessor::class);
        }
        return $this->cartItemOptionsProcessor;
    }
}
