<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Post;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\Error;
use Magento\Framework\Message\MessageInterface;

/**
 * Class Validate
 * @package Aheadworks\Blog\Controller\Adminhtml\Post
 */
class Validate extends \Aheadworks\Blog\Controller\Adminhtml\Post
{
    /**
     * Validate post
     *
     * @param array $response
     * @return void
     */
    private function validate($response)
    {
        $errors = [];
        $requestData = $this->getRequest()->getPostValue();
        if ($postData = $requestData['post']) {
            $postId = isset($postData['id']) && $postData['id']
                ? $postData['id']
                : false;
            try {
                /** @var \Aheadworks\Blog\Api\Data\PostInterface $postDataObject */
                $postDataObject = $this->postDataFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $postDataObject,
                    $this->preparePostData($postData),
                    'Aheadworks\Blog\Api\Data\PostInterface'
                );
                /** @var \Aheadworks\Blog\Model\Post $postModel */
                $postModel = $this->postFactory->create();
                $postModel->setData(
                    $this->dataObjectProcessor->buildOutputDataArray(
                        $postDataObject,
                        'Aheadworks\Blog\Api\Data\PostInterface'
                    )
                );
                if ($postId) {
                    $postModel->setPostId($postId);
                }
                $postModel->validateBeforeSave();

                if ($saveAction = $this->getRequest()->getParam('action')) {
                    if (in_array($saveAction, ['schedule', 'save', 'save_and_continue'])
                        && $this->booleanUtils->toBoolean($postData['is_scheduled'])
                        && strtotime($postModel->getPublishDate()) <= time()
                    ) {
                        throw new LocalizedException(__("Publish date must be in future for scheduled posts"));
                    }
                }
            } catch (\Magento\Framework\Validator\Exception $exception) {
                /* @var $error Error */
                foreach ($exception->getMessages(MessageInterface::TYPE_ERROR) as $error) {
                    $errors[] = $error->getText();
                }
            } catch (LocalizedException $exception) {
                $errors[] = $exception->getMessage();
            }
        }
        if ($errors) {
            $messages = $response->hasMessages() ? $response->getMessages() : [];
            foreach ($errors as $error) {
                $messages[] = $error;
            }
            $response->setMessages($messages);
            $response->setError(1);
        }
    }

    /**
     * AJAX post validate action
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(0);

        $this->validate($response);
        $resultJson = $this->resultJsonFactory->create();
        if ($response->getError()) {
            $response->setError(true);
            $response->setMessages($response->getMessages());
        }

        $resultJson->setData($response);
        return $resultJson;
    }
}
