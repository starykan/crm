<?php

use AmoCRM\Models\ContactModel;

require './vendor/autoload.php';

const DAY_SECONDS = 86400;

$apiClient = new \AmoCRM\Client\AmoCRMApiClient(
    '74757f81-9b61-4a00-9dac-a2ec3b1aef8d',
    'SI18aBWp9iKM6f1AwJsu4VjJoprfqHPEGTRcMZwR168MUDEF171dODmGBGAnoD0q',
    'https://google.com'
);
$apiClient->setAccountBaseDomain('staryk89.amocrm.ru');
$accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode('def50200cf30ebbfebac44712a2403d0b7a7f8e1334e92b6d53cd70e512cd01e344e40de48e9f3cd9c20a7c650af0f5c278ee885f61b29caccf58bfb541c06707f0053c0c5b63860f5867c92fad9f3c5d8ebd860e22f633cca45159c12f9f6a4d5c07e8fbb13e4707956b8e74ff2acb8e6884c1be70be7ed77602cc899a608f79db2de6b67d4d8d6d81d6b75d253cbf36002ac976714fcbcd0549544062a1f47ee3b76d5f5d6a87c33be3195756fc9b64de0ff64732335ebb4103abd59dcf853a749ad7e3847a467b13a577a6f96ff68f93133299b3da664b5d7120655b0c51c9fb7f1dbbe4cb86d6b8917e8f1b104cd236763d7006a8ef8ee9dec5e48cef5d166202d981b284ab4a29b3f7ea63f48dfac085fa5327c22f34aa34e602b3772294aed23af47efd34e8ca674cffb3197af48ba84fb4c915975972c3c36df0a182ced50a30de6538ec964749371cd119d8220adc6bddb071d00ad3850c3426d0d527e089949e1dad8925892ab8b766339b109f3a40ee489c175ae348dc282c756493f30ba49da624331b4dd380d34e00dc5dd6f5d6700be129dfd0c654eefe1ecdc5802adfdceb7b9235141c65bc278c997dd67798c381803a60786cbff');
$apiClient->setAccessToken($accessToken);
$contactList = $apiClient->contacts()->get(null, ['leads']);
/** @var ContactModel $contact */
foreach ($contactList as $contact) {
    $leads = $contact->getLeads();
    if (null === $leads || $leads->isEmpty()) {
        makeTask($contact->getId(), $apiClient);
    }
}

function makeTask(string $contactId, \AmoCRM\Client\AmoCRMApiClient $apiClient): void
{
    $tasksCollection = new \AmoCRM\Collections\TasksCollection();
    $task = new \AmoCRM\Models\TaskModel();
    $task->setText('Контакт без сделок')
        ->setCompleteTill(time() + DAY_SECONDS * 7)// mktime(10, 0, 0, 10, 3, 2020))
        ->setEntityType(\AmoCRM\Helpers\EntityTypesInterface::CONTACTS)
        ->setEntityId($contactId);
    $tasksCollection->add($task);
    $apiClient->tasks()->add($tasksCollection);
}
