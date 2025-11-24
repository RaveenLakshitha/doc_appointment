<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->query('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $patients = Patient::active()
            ->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$q}%"])
            ->orWhere('email', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->select('id', 'first_name', 'middle_name', 'last_name', 'email', 'phone')
            ->limit(10)
            ->get()
            ->map(fn($p) => [
                'id'        => $p->id,
                'full_name' => $p->full_name,
                'email'     => $p->email,
                'phone'     => $p->phone,
            ]);

        return response()->json($patients);
    }
}