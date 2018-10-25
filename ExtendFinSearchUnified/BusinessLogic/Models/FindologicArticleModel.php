<?php

namespace ExtendFinSearchUnified\BusinessLogic\Models;

use FinSearchUnified\BusinessLogic\Models\FindologicArticleModel as OriginalFindologicArticleModel;
use Shopware\Models\Article\Article;
use Shopware\Models\Category\Category;

class FindologicArticleModel extends OriginalFindologicArticleModel
{
    public function __construct(Article $shopwareArticle, $shopKey, array $allUserGroups, array $salesFrequency, Category $baseCategory)
    {
        parent::__construct($shopwareArticle, $shopKey, $allUserGroups, $salesFrequency, $baseCategory);
    }

    /*
     * Example on how to add custom properties to the item
     */
    public function setProperties()
    {
        parent::setProperties();
    }
}
