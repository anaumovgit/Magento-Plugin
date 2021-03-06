<?php

declare(strict_types=1);

namespace Learning\CheckoutMessage\Test\Unit\Plugin\Magento\Checkout;

use Learning\CheckoutMessage\Plugin\Magento\Checkout\PaymentInformationManagementPlugin;
use Magento\Checkout\Model\PaymentInformationManagement;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order as ResourceOrder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\Data\PaymentExtensionInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Framework\Message\ManagerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers PaymentInformationManagementPlugin
 */
class PaymentInformationManagementPluginTest extends TestCase
{
    /**
     * @var PaymentInformationManagementPlugin
     */
    private PaymentInformationManagementPlugin $object;

    /**
     * @var OrderRepositoryInterface|MockObject
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var Order|MockObject
     */
    private Order $order;

    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;

    /**
     * @var ResourceOrder|MockObject
     */
    private ResourceOrder $orderResource;

    /**
     * @var OrderInterface|MockObject
     */
    private OrderInterface $orderInterface;

    /**
     * @var PaymentInformationManagement|MockObject
     */
    private PaymentInformationManagement $subject;

    /**
     * @var PaymentInterface|MockObject
     */
    private PaymentInterface $paymentMethod;

    /**
     * @var PaymentExtensionInterface|MockObject
     */
    private PaymentExtensionInterface $extensionAttributes;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->orderRepository = $this->getMockBuilder(OrderRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageManager = $this->getMockBuilder(ManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderResource = $this->getMockBuilder(ResourceOrder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderInterface = $this->getMockBuilder(OrderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->subject = $this->getMockBuilder(PaymentInformationManagement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->paymentMethod = $this->getMockBuilder(PaymentInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->extensionAttributes = $this->getMockBuilder(PaymentExtensionInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new PaymentInformationManagementPlugin(
            $this->orderRepository,
            $this->orderResource,
            $this->messageManager
        );
    }

    /**
     * @return void
     */
    public function testAfterSavePaymentInformationAndPlaceOrder(): void
    {
        $this->orderRepository->expects($this->any())
            ->method('get')
            ->with('51')
            ->willReturn($this->order);

        $this->order->expects($this->once())
            ->method('addCommentToStatusHistory')
            ->willReturn($this->orderInterface);

        $this->order->expects($this->once())
            ->method('setCustomerNote')
            ->willReturn($this->orderInterface);

        $this->object->afterSavePaymentInformationAndPlaceOrder(
            $this->subject,
            '51',
            58,
            $this->paymentMethod
        );
    }

    /**
     * @return void
     * @exception \Exception
     */
    public function testAfterSavePaymentInformationAndPlaceOrderWithException(): void
    {
        $this->orderRepository->expects($this->any())
            ->method('get')
            ->with('51')
            ->willReturn($this->order);

        $this->order->expects($this->once())
            ->method('addCommentToStatusHistory')
            ->willReturn($this->orderInterface);

        $this->order->expects($this->once())
            ->method('setCustomerNote')
            ->willReturn($this->orderInterface);

        $this->orderResource->expects($this->once())
            ->method('save')
            ->with($this->order)
            ->willThrowException(new \Exception('Exception'));

        $this->object->afterSavePaymentInformationAndPlaceOrder(
            $this->subject,
            '51',
            58,
            $this->paymentMethod
        );
    }

    /**
     * @dataProvider getCommentProvider
     *
     * @param string|null $actual
     * @param string|null $expected
     * @return void
     */
    public function testGetComment(?string $actual, ?string $expected): void
    {
        $this->extensionAttributes->expects($this->any())
            ->method('getComment')
            ->willReturn($actual);

        $this->assertEquals($expected, $this->object->getComment($this->extensionAttributes));
    }

    /**
     * @return \string[][]
     */
    public function getCommentProvider(): array
    {
        return [
            ['', ''],
            ['Comment', 'Comment'],
            ['   Comment ', 'Comment'],
            [null, '']
        ];
    }
}
