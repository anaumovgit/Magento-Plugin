<?php

declare(strict_types=1);

namespace Learning\ExtensionAttribute\Model;

use Magento\Framework\Model\AbstractModel;
use Learning\ExtensionAttribute\Model\ResourceModel\LearningExtensionAttribute as ResourceModel;
use Learning\ExtensionAttribute\Api\Data\LearningExtensionAttributeInterface;

class LearningExtensionAttribute extends AbstractModel implements LearningExtensionAttributeInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    public function getDescription(): string
    {
        return $this->_getData(self::DESCRIPTION);
    }

    public function getProductId(): int
    {
        return $this->_getData(self::PRODUCT_ID);
    }

    public function setDescription(string $description): LearningExtensionAttribute
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    public function setProductId(int $productId): LearningExtensionAttribute
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }
}
