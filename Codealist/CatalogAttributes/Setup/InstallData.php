<?php
namespace Codealist\CatalogAttributes\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * Set factory
     *
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    private $attributeSetFactory;
    /**
     * Constructor
     *
     * @param \Magento\Eav\Setup\EavSetupFactory             $eavSetupFactory
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * Function install
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $defaultAttributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);




        /********* BEGIN: Create Default Attribute SET ********* */

        /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
        $attributeSetName = "My Attribute Set";
        $attributeSet = $this->attributeSetFactory->create();
        $attributeSet->setEntityTypeId($entityTypeId)->load($attributeSetName, 'attribute_set_name');
        if ($attributeSet->getId()) {
            throw new AlreadyExistsException(__('Attribute Set already exists.'));
        }
        $attributeSet->setAttributeSetName($attributeSetName)->validate();
        $attributeSet->save();
        $attributeSet->initFromSkeleton($defaultAttributeSetId)->save();

        // Get the attribute group
        $attributeGroupId = $eavSetup->getAttributeGroupId(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeSet->getId(),
            'General'
        );
        /********* END ********* */




        /********* BEGIN: Add TEXT attribute ********* */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'process',
            [
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'Process',
                'required' => false,
                'user_defined' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            ]
        );
        $eavSetup->addAttributeToGroup(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeSet->getId(),
            $attributeGroupId,
            'process',
            '1' // Sort Order
        );
        /********* END ********* */





        /********* BEGIN: Add TEXTAREA attribute ********* */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'product_features',
            [
                'type' => 'text',
                'input' => 'textarea',
                'label' => 'Product features',
                'required' => false,
                'user_defined' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            ]
        );
        $eavSetup->addAttributeToGroup(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeSet->getId(),
            $attributeGroupId,
            'product_features',
            '2' // Sort Order
        );
        /********* END ********* */




        /********* BEGIN: Add DROPDOWN/SELECT attribute ********* */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'length',
            [
                'type' => 'int',
                'input' => 'select',
                'label' => 'Length',
                'required' => false,
                'user_defined' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'option' => [
                    'values' => [ '4.281', '4.375', '4.5' ]
                ]
            ]
        );
        $eavSetup->addAttributeToGroup(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeSet->getId(),
            $attributeGroupId,
            'length',
            '3' // Sort Order
        );
        /********* END ********* */



        $setup->endSetup();


    }
}