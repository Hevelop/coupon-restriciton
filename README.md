Hevelop_CouponRestriction
-------------------------

#### Version: 1.0.0 


This module implements restriction for a coupon code.

This restriction will be applied to the CPR (Catalog Price Rule)

Example:
* My cart has 2 products:
    - SKU 0001 => 10$
    - SKU 0002 => 15$
* SKU 0001 has a CPR, so his price will be (for example) 5$
* I try to apply the coupon TEST10 that give me a discount of 10%
* This coupon will be applied only for SKU 0002

## Configuration

* Go to Marketing -> Cart Price Rule
* Edit or Create a rule
* On the *Conditions* section under the *Action* tab, add the following row_
    * **Exclude products with a Catalog Price Rule Is Yes**
    * This condition allow the coupon code only to the cart items that haven't a Catalog Price rule
    
## For Developers

* The model that implement the new condition type, is configurable from the di.xml
    * List of arguments that are configurable:
        * form_name
        * attribute
        * attribute_label
        * attribute_to_check
        * custom_value_element_type
        * custom_input_type
        * custom_rule_operator
    
