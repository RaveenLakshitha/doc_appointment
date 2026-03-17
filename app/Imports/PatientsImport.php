<?php

namespace App\Imports;

use App\Models\Patient;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class PatientsImport implements ToModel, WithHeadingRow, WithValidation
{
    protected function mapRow(array $row)
    {
        // Define internal keys to localized keys mapping (for both EN and ES)
        $mapping = [
            'first_name'     => ['first_name', 'nombre'],
            'middle_name'    => ['middle_name', 'segundo_nombre'],
            'last_name'      => ['last_name', 'apellido'],
            'date_of_birth'  => ['date_of_birth', 'fecha_de_nacimiento'],
            'age'            => ['age', 'edad'],
            'gender'         => ['gender', 'genero'],
            'phone'          => ['phone', 'telefono'],
            'email'          => ['email', 'correo_electronico'],
            'address'        => ['address', 'direccion'],
            'city'           => ['city', 'ciudad'],
            'state'          => ['state', 'estado'],
            'zip_code'       => ['zip_code', 'codigo_postal'],
            'marital_status' => ['marital_status', 'estado_civil'],
        ];

        $mapped = [];
        foreach ($mapping as $internal => $localizedKeys) {
            $value = null;
            foreach ($localizedKeys as $key) {
                // Maatwebsite Excel slugifies headers: "First Name" -> "first_name", "Segundo Nombre" -> "segundo_nombre"
                $slug = \Illuminate\Support\Str::slug($key, '_');
                if (isset($row[$slug])) {
                    $value = $row[$slug];
                    break;
                }
            }
            $mapped[$internal] = $value;
        }

        return $mapped;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        Log::info('PatientsImport - Raw Row:', $row);
        $mappedRow = $this->mapRow($row);
        Log::info('PatientsImport - Mapped Row:', $mappedRow);

        $patient = Patient::withTrashed()
            ->where(function ($q) use ($mappedRow) {
                if (!empty($mappedRow['email'])) {
                    $q->where('email', $mappedRow['email']);
                }
                if (!empty($mappedRow['phone'])) {
                    $q->orWhere('phone', $mappedRow['phone']);
                }
            })
            ->first();

        $data = [
            'first_name'         => $mappedRow['first_name'],
            'middle_name'        => $mappedRow['middle_name'] ?? null,
            'last_name'          => $mappedRow['last_name'],
            'date_of_birth'      => $mappedRow['date_of_birth'] ?? null,
            'age'                => $mappedRow['age'],
            'gender'             => strtolower($mappedRow['gender']),
            'phone'              => $mappedRow['phone'] ?? null,
            'email'              => $mappedRow['email'] ?? null,
            'address'            => $mappedRow['address'] ?? null,
            'city'               => $mappedRow['city'] ?? null,
            'state'              => $mappedRow['state'] ?? null,
            'zip_code'           => $mappedRow['zip_code'] ?? null,
            'marital_status'     => $mappedRow['marital_status'] ?? null,
            'is_active'          => true,
            'is_deleted'         => false,
        ];

        // Clean up gender (handle Spanish input)
        $genderMap = [
            'masculino' => 'male',
            'femenino'  => 'female',
            'otro'      => 'other',
        ];

        $genderValue = strtolower($data['gender']);
        if (isset($genderMap[$genderValue])) {
            $data['gender'] = $genderMap[$genderValue];
        }

        // Clean up marital status (handle Spanish input)
        if (!empty($data['marital_status'])) {
            $maritalStatusMap = [
                'soltero'     => 'single',
                'soltera'     => 'single',
                'casado'      => 'married',
                'casada'      => 'married',
                'divorciado'  => 'divorced',
                'divorciada'  => 'divorced',
                'viudo'       => 'widowed',
                'viuda'       => 'widowed',
            ];
            
            $msValue = strtolower($data['marital_status']);
            if (isset($maritalStatusMap[$msValue])) {
                $data['marital_status'] = $maritalStatusMap[$msValue];
            }
        }

        if ($patient) {
            Log::info('PatientsImport - Updating existing patient ID: ' . $patient->id);
            if ($patient->trashed()) {
                $patient->restore();
            }
            $patient->update($data);
            return null;
        }

        $lastPatient = Patient::orderBy('id', 'desc')->first();
        $nextNumber = $lastPatient ? $lastPatient->id + 1 : 1;
        $data['medical_record_number'] = 'MRN-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        Log::info('PatientsImport - Creating new patient with data:', $data);

        return new Patient($data);
    }

    public function rules(): array
    {
        // We need to validate based on the actual keys in the row, but since we map them,
        // it's better to perform manual validation or update the rules dynamically.
        // For simplicity with WithValidation, we'll keep internal keys but use a custom validator if needed.
        // However, WithValidation runs BEFORE model(), so it uses the raw row keys.
        
        // Let's create a dynamic ruleset that checks for either English or Spanish keys.
        $rules = [];
        
        // Map of required fields to their potential slugified headers
        $requiredFields = [
            'first_name' => ['first_name', 'nombre'],
            'last_name'  => ['last_name', 'apellido'],
            'age'        => ['age', 'edad'],
            'gender'     => ['gender', 'genero'],
        ];

        foreach ($requiredFields as $field => $options) {
            $slugs = array_map(fn($o) => \Illuminate\Support\Str::slug($o, '_'), $options);
            // We'll use a rule that says "at least one of these must be present" or just check the current locale
            // Actually, Maatwebsite validation allows rule keys to be the headers.
            // If we don't know which one user uploaded, we can add both as nullable but required_without_all.
        }

        // Simpler approach: validate the internal keys after mapping if possible, 
        // OR just allow both.
        
        return [
            '*.nombre' => 'required_without:*.first_name',
            '*.first_name' => 'required_without:*.nombre',
            '*.apellido' => 'required_without:*.last_name',
            '*.last_name' => 'required_without:*.apellido',
            '*.edad' => 'required_without:*.age',
            '*.age' => 'required_without:*.edad',
            '*.genero' => 'required_without:*.gender',
            '*.gender' => 'required_without:*.genero',
        ];
    }
}
