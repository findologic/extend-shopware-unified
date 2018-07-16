<?php

namespace ExtendFinSearchUnified\BusinessLogic;

use ExtendFinSearchUnified\BusinessLogic\Models\FindologicArticleModel;
use Shopware\Models\Article\Article;
use Shopware\Models\Category\Category;

class FindologicArticleFactory
{
    public function create(Article $shopwareArticle, $shopKey, array $allUserGroups, array $salesFrequency, Category $baseCategory)
    {
        return new FindologicArticleModel($shopwareArticle, $shopKey, $allUserGroups, $salesFrequency, $baseCategory);
    }
}
