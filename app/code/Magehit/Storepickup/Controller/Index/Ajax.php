<?php
 
namespace Magehit\Storepickup\Controller\Index;
 
use Magento\Framework\App\Action\Context;
use Magehit\Storepickup\Model\StorepickupFactory;
class Ajax extends \Magento\Framework\App\Action\Action
{
    protected $_rawResultFactory;
    protected $_StorepickupFactory;
    protected $dataHelper;
    protected $_serialize;
	protected $_checkoutSession;

    public function __construct(
        Context $context,
        StorepickupFactory $StorepickupFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magehit\Storepickup\Helper\Data $dataHelper,
        \Magehit\Storepickup\Serialize\Serializer\Json $serialize
    )
    {
        $this->_rawResultFactory = $jsonResultFactory;
        $this->_StorepickupFactory = $StorepickupFactory;
        $this->dataHelper = $dataHelper;
        $this->_serialize = $serialize;
        parent::__construct($context);
    }
 
    public function execute()
    {
		$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
		$checkoutSession = $objectManager->get('\Magento\Checkout\Model\Session');
        $result = $this->_rawResultFactory->create();
        $kq=['success' => false];
        $storeid  = $this->getRequest()->getParam('store');
        $layout = $this->_view->getLayout();
        if($storeid){
            $data = $this->_StorepickupFactory->create()->load($storeid);
            $day = [];
            $time = [];
            $i= 0;
            //var_dump($this->_serialize->unserialize($data->getSchedule()));
            foreach ($this->_serialize->unserialize($data->getSchedule()) as  $value) {
               
               if($value['status']== 0){
                $day[]=$i;
                }else{
                    $time[$i]['from'] = $value['from'];
                    $time[$i]['to'] = $value['to'];
                }
               $i++;
            }
            $html = $layout->createBlock(\Magehit\Storepickup\Block\Ajax::class)->setTemplate('Magehit_Storepickup::ajax.phtml')->setResponse($storeid)->toHtml();
			$checkoutSession->setPickupId($storeid);
			$checkoutSession->getQuote()->getShippingAddress()->setShippingMethod('storepickup_storepickup');
			$checkoutSession->getQuote()->getShippingAddress()->setCollectShippingRates(true)->collectShippingRates();
			$checkoutSession->getQuote()->getShippingAddress()->requestShippingRates();
			$checkoutSession->getQuote()->getShippingAddress()->save();
			$checkoutSession->getQuote()->collectTotals();
			$checkoutSession->getQuote()->save();
			$pickupData = $cartModel->getQuote()->getStorepickupData() ? $this->_serialize->unserialize($cartModel->getQuote()->getStorepickupData()) : null;
			if(count($pickupData) && $pickupData['id'] == $storeid){
				$cartModel->getQuote()->setStorepickupData($this->_serialize->serialize($pickupData));
			}else{
				$cartModel->getQuote()->setStorepickupData($this->_serialize->serialize(array(
					'id' => $storeid
				)));
			}
            $kq=['success' => true,'day'=>$day,'time'=>$time,'html'=>$html, 'pickupData'=> $pickupData];
        }
        
        $result->setData($kq);
        return $result;
    }
}