<?php

namespace App\Services;

use App\Repositories\ContactRepository;
use Illuminate\Support\Facades\Cache;

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
        // Cache contact info for 30 seconds for better performance
        return Cache::remember('contact_info', 30, function () {
            return $this->contactRepository->getFirst();
        });
    }
}
