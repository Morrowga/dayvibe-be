<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Interfaces\ScannerRepositoryInterface;

class ScannerController extends Controller
{
    private ScannerRepositoryInterface $scannerRepository;

    public function __construct(ScannerRepositoryInterface $scannerRepository)
    {
        $this->scannerRepository = $scannerRepository;
    }

    public function index()
    {
        return Inertia::render('Scanner');
    }

    public function store(Request $request)
    {
        $data = $this->scannerRepository->store($request);

        return $data;
    }

    public function order(Request $request)
    {
        $data = $this->scannerRepository->order($request);

        return $data;
    }
}
