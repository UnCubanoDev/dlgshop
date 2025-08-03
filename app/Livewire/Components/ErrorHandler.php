<?php

namespace App\Livewire\Components;

use Livewire\Component;

class ErrorHandler extends Component
{
    public function render()
    {
        return view('livewire.components.error-handler');
    }

    public function handleError($error)
    {
        // Log the error
        \Log::error('Livewire Error', [
            'error' => $error,
            'url' => request()->url(),
            'user' => auth()->id(),
        ]);

        // Return a safe response
        return response()->json(['error' => 'An error occurred'], 500);
    }
}
