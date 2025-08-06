php artisan tinker

$user = \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => \Illuminate\Support\Facades\Hash::make('password123'),
]);
$user->assignRole('super_admin');
