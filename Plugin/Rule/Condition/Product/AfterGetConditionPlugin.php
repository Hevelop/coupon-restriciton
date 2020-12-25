<?php
/**
 * Copyright © Hevelop srl. All rights reserved.
 * @license https://opensource.org/licenses/agpl-3.0  AGPL-3.0 License
 * @author Nicolò Dian <nicolo@hevelop.com>
 * @copyright Copyright (c) 2020 Hevelop srl (https://hevelop.com)
 * @package Hevelop_CouponRestriction
 */
namespace Hevelop\CouponRestriction\Plugin\Rule\Condition\Product;

use Hevelop\CouponRestriction\Model\Rule\Condition\NoCatalogRuleProduct;
use Magento\SalesRule\Model\Rule\Condition\Product\Combine;


class AfterGetConditionPlugin
{
    
    /**
     * @param Combine $subject
     * @param $result
     * @return array
     */
    public function afterGetNewChildSelectOptions(Combine $subject, $result)
    {
        $data = $result;
        $newConditions = [
            [
                'value' => [
                    [
                        'value' => NoCatalogRuleProduct::class . "|" .NoCatalogRuleProduct::CODE,
                        'label' => __('Apply restriction for Catalog Price Rule'),
                    ]
                ],
                'label' => __("Exclude Catalog Price Rule")
            ]
        ];
    
        $result = array_merge_recursive($data,$newConditions);
        
        return $result;
    }
}
