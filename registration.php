<?php
/**
 * Copyright © Hevelop srl. All rights reserved.
 * @license https://opensource.org/licenses/agpl-3.0  AGPL-3.0 License
 * @author Nicolò Dian <nicolo@hevelop.com>
 * @copyright Copyright (c) 2020 Hevelop srl (https://hevelop.com)
 * @package Hevelop_CouponRestriction
 */
use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Hevelop_CouponRestriction',
    __DIR__
);
