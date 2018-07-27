<?php

namespace FinSearchCustomized\Bundles;

use FinSearchUnified\Bundles\ProductNumberSearch as OriginalProductNumberSearch;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

class ProductNumberSearch extends OriginalProductNumberSearch
{
    public function search(Criteria $criteria, ShopContextInterface $context)
    {
        return parent::search($criteria, $context);
    }
}
