<?php 
namespace Icube\Giftcard\Plugin;

use Aheadworks\Giftcard\Model\GiftcardRepository as Subject;
use Aheadworks\Giftcard\Model\Source\EmailStatus;
use Aheadworks\Giftcard\Model\Source\Giftcard\EmailTemplate;
use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;

class Giftcard
{
    private $giftcardRepository;

    public function __construct(
        GiftcardRepositoryInterface $giftcardRepository
    ) { 
        $this->giftcardRepository = $giftcardRepository;
    }

    public function afterSave(Subject $subject, $giftcard)
    {
        // Set Email Sent To Not Send if Email Template is Don't Send
        if($giftcard->getEmailTemplate() == EmailTemplate::DO_NOT_SEND
               && $giftcard->getEmailSent() != EmailStatus::SENT
                && $giftcard->getEmailSent() != EmailStatus::NOT_SEND)
        {
            $giftcardCode = $this->giftcardRepository->get($giftcard->getId());
            $giftcardCode->setEmailSent(EmailStatus::NOT_SEND);
            $this->giftcardRepository->save($giftcardCode); 
        }
        return $giftcard;
    }
}