<?php

namespace App\Imports;

use App\Models\Division;
use App\Models\Education;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    public function __construct(public bool $save = true)
    {
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $major_id = Division::where('name', $row['major'])->first()?->id
            ?? Division::create(['name' => $row['major']])?->id;
        $school_id = Education::where('name', $row['school'])->first()?->id
            ?? Education::create(['name' => $row['school']])?->id;
        $user = (new User)->forceFill([
            'id' => isset($row['id']) ? $row['id'] : null,
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'gender' => $row['gender'],
            'birth_date' => $row['birth_date'],
            'birth_place' => $row['birth_place'],
            'address' => $row['address'],
            'city' => $row['city'],
            'school_id' => $school_id,
            'major_id' => $major_id,
            'password' => Hash::make($row['password']),
            'raw_password' => $row['password'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ]);
        if ($this->save) {
            $user->save();
        }
        return $user;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'string', Rule::unique('users', 'email')],
            'gender' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function onFailure(Failure ...$failures)
    {
    }
}
