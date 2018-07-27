<?php

namespace FinSearchCustomized\BusinessLogic;

use FinSearchCustomized\BusinessLogic\Models\FindologicArticleModel;
use Shopware\Models\Article\Article;
use Shopware\Models\Category\Category;

class FindologicArticleFactory
{
    public function create(Article $shopwareArticle, $shopKey, array $allUserGroups, array $salesFrequency, Category $baseCategory)
    {
        return new FindologicArticleModel($shopwareArticle, $shopKey, $allUserGroups, $salesFrequency, $baseCategory);
    }
}
