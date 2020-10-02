<?php
/**
 * Copyright © Hevelop srl. All rights reserved.
 * @license https://opensource.org/licenses/agpl-3.0  AGPL-3.0 License
 * @author Nicolò Dian <nicolo@hevelop.com>
 * @copyright Copyright (c) 2020 Hevelop srl (https://hevelop.com)
 * @package Hevelop_CouponRestriction
 */
namespace Hevelop\CouponRestriction\Helper;


use Magento\Checkout\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;

class HelperFunction extends AbstractHelper
{
    /**
     * Cached resources singleton
     *
     * @var ResourceConnection
     */
    protected $_resources;
    
    /**
     * @var Session
     */
    protected $_checkoutSession;
    
    /**
     * HelperFunction constructor.
     * @param Context $context
     * @param Session $checkoutSession
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        ResourceConnection $resourceConnection
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_resources = $resourceConnection;
        parent::__construct($context);
    }
    
    /**
     * @param $productId
     * @return bool
     */
    public function hasProductACatalogRule($productId): bool
    {
        $connection = $this->_resources->getConnection();
        /** @var Quote $quote */
        try {
            $quote = $this->_checkoutSession->getQuote();
        } catch (NoSuchEntityException $e) {
            return true; // true to not apply coupon
        } catch (LocalizedException $e) {
            return true; // true to not apply coupon
        }
        
        $websiteId = $quote->getStore()->getWebsiteId();
        $customerGroupId = $quote->getCustomerGroupId();
        $date = strtotime($quote->getUpdatedAt());

        $select = $connection->select()
            ->from($this->_resources->getTableName('catalogrule_product'))
            ->where('website_id = ?', $websiteId)
            ->where('(customer_group_id = ? or customer_group_id = 0)', $customerGroupId)
            ->where('product_id = ?', $productId)
            ->where('(from_time = 0 or from_time < ?) or (to_time = 0 or to_time > ?)', $date);
            
        $result = $connection->fetchAll($select);
        if (!$result || count($result) == 0) {
            return false;
        }
        
        return true;
    }
}