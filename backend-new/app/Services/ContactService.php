<?php

namespace App\Services;

use App\Repositories\ContactRepository;

class ContactService extends BaseService
{
    protected $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        parent::__construct($contactRepository);
        $this->contactRepository = $contactRepository;
    }

    public function getContactInfo()
    {
        return $this->contactRepository->getFirst();
    }
}
