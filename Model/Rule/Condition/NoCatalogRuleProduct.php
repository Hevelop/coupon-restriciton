<?php
/**
 * Copyright © Hevelop srl. All rights reserved.
 * @license https://opensource.org/licenses/agpl-3.0  AGPL-3.0 License
 * @author Nicolò Dian <nicolo@hevelop.com>
 * @copyright Copyright (c) 2020 Hevelop srl (https://hevelop.com)
 * @package Hevelop_CouponRestriction
 */
namespace Hevelop\CouponRestriction\Model\Rule\Condition;

use Hevelop\CouponRestriction\Helper\HelperFunction;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductCategoryList;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\Context;
use Magento\Rule\Model\Condition\Product\AbstractProduct;

/**
 * Product rule condition data model
 * @method getCustomValueElementType()
 * @method getCustomInputType()
 * @method getCustomRuleOperator()
 * @method getAttributeToCheck()
 * @method getAttributeLabel()
 *
 * @author Nicolò Dian <nicolo@hevelop.com>
 */
class NoCatalogRuleProduct extends AbstractProduct
{
    const CODE = "no_catalog_rule";
    
    /**
     * @var HelperFunction 
     */
    protected $_helper;
    /**
     * @var Yesno
     */
    protected $yesno;
    
    /**
     * NoCatalogRuleProduct constructor.
     * @param Context $context
     * @param Data $backendData
     * @param Config $config
     * @param ProductFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param Product $productResource
     * @param Collection $attrSetCollection
     * @param FormatInterface $localeFormat
     * @param HelperFunction $_helper
     * @param Yesno $yesno
     * @param array $data
     * @param ProductCategoryList|null $categoryList
     */
    public function __construct(
        Context $context,
        Data $backendData,
        Config $config,
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        Product $productResource,
        Collection $attrSetCollection,
        FormatInterface $localeFormat,
        HelperFunction $_helper,
        Yesno $yesno,
        array $data = [],
        ProductCategoryList $categoryList = null
    ) {
        $this->_helper = $_helper;
        $this->yesno = $yesno;
        parent::__construct($context, $backendData, $config, $productFactory, $productRepository, $productResource, $attrSetCollection, $localeFormat, $data, $categoryList);
    }
    
    /**
     * Add special attributes
     *
     * @param array $attributes
     * @return void
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);
        $attributes[$this->getAttribute()] = __($this->getAttributeLabel());
    }

    /**
     * Retrieve attribute
     *
     * @return string
     */
    public function getAttribute(): string
    {
        $attribute = $this->getData('attribute');
        if (strpos($attribute, '::') !== false) {
            list(, $attribute) = explode('::', $attribute);
        }

        return (string)$attribute;
    }

    /**
     * @inheritdoc
     */
    public function getAttributeName()
    {
        $attribute = $this->getAttribute();
        if ($this->getAttributeScope()) {
            $attribute = $this->getAttributeScope() . '::' . $attribute;
        }

        return $this->getAttributeOption($attribute);
    }
    
    
    /**
     * @return $this|AbstractProduct
     */
    public function loadAttributeOptions()
    {
        $attributes = [];
        $this->_addSpecialAttributes($attributes);
        
        asort($attributes);
        $this->setAttributeOption($attributes);
        
        return $this;
    }
    
    /**
     * Set attribute value
     *
     * @param string $value
     * @return void
     */
    public function setAttribute(string $value)
    {
        if (strpos($value, '::') !== false) {
            list($scope, $attribute) = explode('::', $value);
            $this->setData('attribute_scope', $scope);
            $this->setData('attribute', $attribute);
        } else {
            $this->setData('attribute', $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function loadArray($arr)
    {
        parent::loadArray($arr);
        $this->setAttributeScope($arr['attribute_scope'] ?? null);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function asArray(array $arrAttributes = [])
    {
        $out = parent::asArray($arrAttributes);
        $out['attribute_scope'] = $this->getAttributeScope();

        return $out;
    }
    
    /**
     * Validate Product Rule Condition
     *
     * @param AbstractModel $model
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function validate(AbstractModel $model)
    {
        //@todo reimplement this method when is fixed MAGETWO-5713
        /** @var \Magento\Catalog\Model\Product $product */
        
        if ($this->getValue() == 0 || $this->getValue() == "0") {
            return true;
        }
    
        $product = $model->getProduct();
        if (!$product instanceof \Magento\Catalog\Model\Product) {
            $product = $this->productRepository->getById($model->getProductId());
        }
        
        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return false;
        }
        
        if ($this->_helper->hasProductACatalogRule($model->getProductId())) {
            return false;
        }

        return true;
    }
    
    /**
     * Assign the new input type if choose "No catalog Price Rule"
     *
     * @return string
     */
    public function getInputType()
    {
        if ($this->getAttribute() == $this->getAttributeToCheck()) {
            return $this->getCustomInputType();
        }
        return parent::getInputType();
    }
    
    /**
     * Change operators for input "No Catalog Price Rule"
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        parent::getDefaultOperatorInputByType();
        $this->_defaultOperatorInputByType[$this->getCustomInputType()] = [
            $this->getCustomRuleOperator()
        ];
        return $this->_defaultOperatorInputByType;
    }
    
    /**
     * Override the default value element type.
     *
     * @return string
     */
    public function getValueElementType()
    {
        return $this->getCustomValueElementType();
    }
    
    /**
     * @return array
     */
    public function getValueSelectOptions()
    {
        $opts = parent::getValueSelectOptions();
        if (!$opts) {
            $opts = $this->yesno->toOptionArray();
        }
        return $opts;
    }

}
