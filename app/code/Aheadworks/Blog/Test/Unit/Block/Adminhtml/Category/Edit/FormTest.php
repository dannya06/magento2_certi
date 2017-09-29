<?php
namespace Aheadworks\Blog\Test\Unit\Block\Adminhtml\Category\Edit;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Block\Adminhtml\Category\Edit\Form
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    const CATEGORY_ID = 1;
    const CATEGORY_NAME = 'Category';
    const STORE_ID = 1;

    const FORM_DATA = [
        'id' => self::CATEGORY_ID,
        'name' => self::CATEGORY_NAME
    ];

    /**
     * @var \Aheadworks\Blog\Block\Adminhtml\Category\Edit\Form
     */
    private $block;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    private $backendSession;

    /**
     * @var \Magento\Framework\Data\Form|\PHPUnit_Framework_MockObject_MockObject
     */
    private $form;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $fieldSetStub = $this->getMock('Magento\Framework\Data\Form\Element\Fieldset', ['addField'], [], '', false);
        $this->form = $this->getMock(
            'Magento\Framework\Data\Form',
            ['setUseContainer', 'addFieldset', 'addValues'],
            [],
            '',
            false
        );
        $this->form->expects($this->any())
            ->method('addFieldset')
            ->will($this->returnValue($fieldSetStub));
        $formFactoryStub = $this->getMock('Magento\Framework\Data\FormFactory', ['create'], [], '', false);
        $formFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->form));

        $systemStoreStub = $this->getMock('Magento\Store\Model\System\Store', ['getStoreValuesForForm'], [], '', false);
        $systemStoreStub->expects($this->any())
            ->method('getStoreValuesForForm')
            ->will($this->returnValue([]));

        $categoryStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\CategoryInterface');
        $categoryRepositoryStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\CategoryRepositoryInterface');
        $categoryRepositoryStub->expects($this->any())
            ->method('get')
            ->with($this->equalTo(self::CATEGORY_ID))
            ->will($this->returnValue($categoryStub));

        $dataObjectProcessorStub = $this->getMock(
            'Magento\Framework\Reflection\DataObjectProcessor',
            ['buildOutputDataArray'],
            [],
            '',
            false
        );
        $dataObjectProcessorStub->expects($this->any())
            ->method('buildOutputDataArray')
            ->with(
                $this->equalTo($categoryStub),
                $this->equalTo('Aheadworks\Blog\Api\Data\CategoryInterface')
            )
            ->will($this->returnValue(self::FORM_DATA));

        $this->request = $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface');
        $this->backendSession = $this->getMock('Magento\Backend\Model\Session', ['getFormData'], [], '', false);

        $storeStub = $this->getMockForAbstractClass('Magento\Store\Api\Data\StoreInterface');
        $storeStub->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::STORE_ID));
        $storeManagerStub = $this->getMockForAbstractClass('Magento\Store\Model\StoreManagerInterface');
        $storeManagerStub->expects($this->any())
            ->method('hasSingleStore')
            ->will($this->returnValue(false));
        $storeManagerStub->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($storeStub));
        $context = $objectManager->getObject(
            'Magento\Backend\Block\Template\Context',
            [
                'request' => $this->request,
                'storeManager' => $storeManagerStub,
                'backendSession' => $this->backendSession
            ]
        );

        $this->block = $objectManager->getObject(
            'Aheadworks\Blog\Block\Adminhtml\Category\Edit\Form',
            [
                'context' => $context,
                'formFactory' => $formFactoryStub,
                'systemStore' => $systemStoreStub,
                'dataObjectProcessor' => $dataObjectProcessorStub,
                'categoryRepository' => $categoryRepositoryStub
            ]
        );
        $this->block->setTemplate(null);
    }

    /**
     * Testing of prepare form
     *
     * @dataProvider prepareFormDataProvider
     */
    public function testPrepareForm($categoryId, $sessionData, $formData)
    {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('cat_id'))
            ->willReturn($categoryId);
        $this->backendSession->expects($this->any())
            ->method('getFormData')
            ->willReturn($sessionData);
        $this->form->expects($this->once())
            ->method('addValues')
            ->with($this->equalTo($formData));
        $this->block->toHtml();
        $this->assertEquals($this->form, $this->block->getForm());
    }

    /**
     * @return array
     */
    public function prepareFormDataProvider()
    {
        return [
            [null, null, []],
            [self::CATEGORY_ID, null, self::FORM_DATA],
            [null, self::FORM_DATA, self::FORM_DATA]
        ];
    }
}
