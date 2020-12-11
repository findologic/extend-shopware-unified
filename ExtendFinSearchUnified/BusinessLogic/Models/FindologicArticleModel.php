<?php

namespace ExtendFinSearchUnified\BusinessLogic\Models;

use Exception;
use FINDOLOGIC\Export\Data\Property;
use FinSearchUnified\BusinessLogic\Models\FindologicArticleModel as OriginalFindologicArticleModel;
use FinSearchUnified\Helper\StaticHelper;
use Shopware\Bundle\MediaBundle\MediaService;
use Shopware\Models\Article\Article;
use Shopware\Models\Article\Detail;
use Shopware\Models\Article\Image;
use Shopware\Models\Category\Category;
use Shopware\Models\Media\Media;

class FindologicArticleModel extends OriginalFindologicArticleModel
{
    // The variant column the variants should be differentiated by
    const VARIANT_TYPE = 'Farbe';

    const URL_REPLACEMENTS = [
        '[' => '%5B',
        ']' => '%5D'
    ];

    /**
     * @var MediaService
     */
    protected $mediaService;

    public function __construct(
        Article $shopwareArticle,
        $shopKey,
        array $allUserGroups,
        array $salesFrequency,
        Category $baseCategory
    ) {
        parent::__construct($shopwareArticle, $shopKey, $allUserGroups, $salesFrequency, $baseCategory);

//        $this->mediaService = Shopware()->Container()->get('shopware_media.media_service');
//
//        if ($this->legacyStruct) {
//            $this->addVariantsJson();
//        }
    }

    /*
     * Example on how to add custom properties to the item
     */
    public function setProperties()
    {
        parent::setProperties();
    }

    /*
     * Example on how to add a property with all variants as a JSON.
     * Each variant includes the corresponding URL and assigned images.
     */
    protected function addVariantsJson()
    {
        $variants = [];

        /** @var Detail $variant */
        foreach ($this->variantArticles as $variant) {
            if (!$variant->getActive() ||
                count($variant->getConfiguratorOptions()) === 0 ||
                (Shopware()->Config()->get('hideNoInStock') && $variant->getInStock() < 1)
            ) {
                continue;
            }

            // Get size and color values
            foreach ($variant->getConfiguratorOptions() as $option) {
                if (StaticHelper::isEmpty($option->getName()) ||
                    StaticHelper::isEmpty($option->getGroup()->getName())
                ) {
                    continue;
                }

                $variantType = StaticHelper::removeControlCharacters($option->getGroup()->getName());
                $variantValue = StaticHelper::removeControlCharacters($option->getName());

                if ($variantType === self::VARIANT_TYPE && !isset($variants[$variantValue])) {
                    $variants[$variantValue] = $this->getNewVariant($variant);
                }
            }
        }

        if (!StaticHelper::isEmpty($variants)) {
            $this->xmlArticle->addProperty(
                new Property('variants', ['' => json_encode($variants)])
            );
        }
    }

    /**
     * @param Detail $variant
     * @return array
     */
    protected function getNewVariant($variant)
    {
        return [
            'productUrl' => $this->getUrlByVariant($variant),
            'images' => $this->getVariantImages($variant),
        ];
    }

    /**
     * @param Detail $variant
     * @return string[]
     */
    protected function getVariantImages($variant)
    {
        $images = [];

        /** @var Image $image */
        foreach ($variant->getImages() as $image) {
            if (!$image->getParent() || $image->getParent()->getMedia() === null) {
                continue;
            }

            $imageUrl = $this->getImageUrlByImage($image->getParent());
            if ($imageUrl) {
                $images[$image->getPosition()] = $imageUrl;
            }
        }

        return array_values($images);
    }

    /**
     * @param Image $image
     * @return string|null
     */
    protected function getImageUrlByImage($image)
    {
        /** @var Image $imageRaw */
        $imageRaw = $image->getMedia();
        if (!($imageRaw instanceof Media) || $imageRaw === null) {
            return null;
        }

        try {
            $imageDetails = $imageRaw->getThumbnailFilePaths();
            $imageDefault = $imageRaw->getPath();
        } catch (Exception $ex) {
            // Entity removed
            return null;
        }

        if (count($imageDetails) > 0) {
            return strtr($this->mediaService->getUrl($imageDefault), self::URL_REPLACEMENTS);
        }

        return null;
    }

    /**
     * @param Detail $variant
     * @return string
     */
    protected function getUrlByVariant($variant)
    {
        $baseLink = $this->getBaseLinkByVariant($variant);

        return Shopware()->Modules()->Core()->sRewriteLink($baseLink, $variant->getArticle()->getName());
    }

    /**
     * @param Detail $variant
     * @return string
     */
    protected function getBaseLinkByVariant($variant)
    {
        return sprintf(
            '%s?sViewport=detail&sArticle=%s&number=%s',
            Shopware()->Config()->get('baseFile'),
            $variant->getArticle()->getId(),
            $variant->getNumber()
        );
    }
}
