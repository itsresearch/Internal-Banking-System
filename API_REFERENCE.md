# API Reference & Code Examples

## User Model Methods

### Get User's Role

```php
// Get user and their role
$user = auth()->user();
$roleName = $user->role->name;

// Using with eager loading (better performance)
$user = User::with('role')->find($userId);
```

### Check User Role

```php
// Using the hasRole helper method
if (auth()->user()->hasRole('admin')) {
    // User is admin
    echo "Welcome Admin!";
}

// Checking by role ID
if (auth()->user()->role_id === 1) {
    // User is admin
}

// Checking all roles
$currentRole = auth()->user()->role->name;
match($currentRole) {
    'admin' => doAdminThing(),
    'manager' => doManagerThing(),
    'staff' => doStaffThing(),
};
```

### Create User Programmatically

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Create admin user
$admin = User::create([
    'name' => 'John Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('SecurePassword123'),
    'role_id' => 1, // Admin role
    'status' => 'active'
]);

// Create manager user
$manager = User::create([
    'name' => 'Jane Manager',
    'email' => 'manager@example.com',
    'password' => Hash::make('SecurePassword456'),
    'role_id' => 2, // Manager role
    'status' => 'active'
]);

// Create staff user
$staff = User::create([
    'name' => 'Bob Staff',
    'email' => 'staff@example.com',
    'password' => Hash::make('SecurePassword789'),
    'role_id' => 3, // Staff role
    'status' => 'active'
]);
```

---

## Role Model Methods

### Get All Users with Specific Role

```php
use App\Models\Role;

// Get admin role with all its users
$adminRole = Role::with('users')->where('name', 'admin')->first();

foreach ($adminRole->users as $user) {
    echo $user->name; // Print each admin's name
}

// Count users by role
$adminCount = Role::where('name', 'admin')->first()->users()->count();
```

### Create New Role

```php
use App\Models\Role;

$supervisor = Role::create([
    'name' => 'supervisor',
    'description' => 'Supervisor Role'
]);
```

### Get Role by Name

```php
use App\Models\Role;

$adminRole = Role::where('name', 'admin')->first();
$managerRole = Role::where('name', 'manager')->firstOrFail();
```

---

## Authentication & Authorization

### Check Authentication

```php
// In controllers or middleware
if (auth()->check()) {
    // User is authenticated
    $user = auth()->user();
}

// Using middleware shorthand
auth()->guest(); // Returns true if not authenticated
auth()->user();  // Returns authenticated user or null
```

### Check Specific Role

```php
// In controller
if (auth()->user()->hasRole('admin')) {
    // Grant access
} else {
    abort(403, 'Unauthorized');
}

// In routes
Route::get('/admin', function () {
    // ...
})->middleware('auth')->can('admin'); // Requires policy
```

### Conditional Rendering in Blade

```blade
<!-- Show only for admins -->
@if(auth()->user()->hasRole('admin'))
    <button>Admin Settings</button>
@endif

<!-- Show for admins and managers -->
@if(in_array(auth()->user()->role->name, ['admin', 'manager']))
    <button>View Reports</button>
@endif

<!-- Show for non-staff users -->
@unless(auth()->user()->hasRole('staff'))
    <button>Manage Users</button>
@endunless
```

---

## Registration & Login

### Custom Registration Logic

```php
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

// Register with role
$role = Role::findOrFail(request('role_id'));

$user = User::create([
    'name' => request('name'),
    'email' => request('email'),
    'password' => Hash::make(request('password')),
    'role_id' => $role->id,
    'status' => 'active'
]);

auth()->login($user);
```

### Login with Role Check

```php
// Fortify automatically handles this in FortifyServiceProvider
// The redirect handler checks user role and redirects accordingly

// Manual login if needed
if (Auth::attempt(['email' => $email, 'password' => $password])) {
    $user = auth()->user();
    // User is now authenticated
    // Fortify's redirect handler will process the redirect
}
```

---

## Routes & Redirects

### Define Role-Based Routes

```php
// Group routes by role
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin-panel', function () {
        return view('admin.panel');
    });
});

// Multiple roles
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/admin', function () {
        // Admin-only
    })->middleware('role:admin');

    Route::get('/dashboard/manager', function () {
        // Manager-only
    })->middleware('role:manager');

    Route::get('/dashboard/staff', function () {
        // Staff-only
    })->middleware('role:staff');
});
```

### Custom Redirect Based on Role

```php
// In FortifyServiceProvider or controller
Fortify::redirects('login', function () {
    $user = auth()->user();
    return match($user->role->name) {
        'admin' => route('dashboard.admin'),
        'manager' => route('dashboard.manager'),
        'staff' => route('dashboard.staff'),
        default => '/dashboard'
    };
});
```

---

## Database Queries

### Find User by Role

```php
use App\Models\User;

// Get all admins
$admins = User::whereHas('role', function ($query) {
    $query->where('name', 'admin');
})->get();

// Get users by role ID
$managers = User::where('role_id', 2)->get();

// Get user with role loaded
$user = User::with('role')->find($userId);
```

### Get Users by Multiple Roles

```php
$managerial = User::whereHas('role', function ($query) {
    $query->whereIn('name', ['admin', 'manager']);
})->get();
```

### Count Users per Role

```php
$roleCounts = User::select('role_id')
    ->with('role')
    ->groupBy('role_id')
    ->get()
    ->map(function ($user) {
        return [
            'role' => $user->role->name,
            'count' => User::where('role_id', $user->role_id)->count()
        ];
    });
```

---

## Blade Templates

### Display User's Role

```blade
<!-- Show role name -->
<p>Your role: {{ auth()->user()->role->name }}</p>

<!-- Show role description -->
<p>{{ auth()->user()->role->description }}</p>

<!-- Display role-specific content -->
<div>
    @switch(auth()->user()->role->name)
        @case('admin')
            <h1>Admin Dashboard</h1>
            <p>You have full system access.</p>
            @break

        @case('manager')
            <h1>Manager Dashboard</h1>
            <p>You can manage teams and reports.</p>
            @break

        @case('staff')
            <h1>Staff Dashboard</h1>
            <p>You can view tasks and submit reports.</p>
            @break
    @endswitch
</div>
```

### Role-Based Visibility

```blade
<!-- Admin only -->
@role('admin')
    <button>System Settings</button>
@endrole

<!-- Custom helper if you add the directive -->
@admin
    <div class="admin-panel">Admin content</div>
@endadmin

<!-- Multiple roles -->
@if(in_array(auth()->user()->role->name, ['admin', 'manager']))
    <button>View Reports</button>
@endif
```

---

## Validation Rules

### Validate Role Selection (In CreateNewUser)

```php
use Illuminate\Validation\Rule;

$validated = $request->validate([
    'role_id' => [
        'required',
        'integer',
        Rule::exists('roles', 'id'), // Ensure role exists
    ],
]);
```

### Custom Role Validation

```php
$validated = $request->validate([
    'role_id' => [
        'required',
        'integer',
        function ($attribute, $value, $fail) {
            $allowedRoles = [1, 2, 3]; // Admin, Manager, Staff
            if (!in_array($value, $allowedRoles)) {
                $fail("The selected role is invalid.");
            }
        },
    ],
]);
```

---

## Error Handling

### Handle Missing Role

```php
try {
    $user = User::with('role')->findOrFail($userId);
    if (!$user->role) {
        throw new Exception('User has no role assigned');
    }
} catch (Exception $e) {
    // Handle error
    return redirect('/')->with('error', 'User configuration error');
}
```

### Graceful Redirect on Role Missing

```php
// In FortifyServiceProvider
Fortify::redirects('login', function () {
    $user = auth()->user();

    try {
        if (!$user->role) {
            return redirect('/contact-support');
        }

        return match($user->role->name) {
            'admin' => route('dashboard.admin'),
            'manager' => route('dashboard.manager'),
            'staff' => route('dashboard.staff'),
            default => '/dashboard'
        };
    } catch (Exception $e) {
        return '/dashboard'; // Fallback
    }
});
```

---

## Testing Examples

### Test Registration with Role

```php
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_admin_registration_redirects_to_admin_dashboard()
    {
        $response = $this->post('/register', [
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role_id' => 1, // Admin role
            'terms' => true,
        ]);

        $response->assertRedirect('/dashboard/admin');
        $this->assertDatabaseHas('users', [
            'email' => 'admin@test.com',
            'role_id' => 1,
        ]);
    }

    public function test_manager_registration_redirects_to_manager_dashboard()
    {
        $response = $this->post('/register', [
            'name' => 'Manager User',
            'email' => 'manager@test.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role_id' => 2, // Manager role
            'terms' => true,
        ]);

        $response->assertRedirect('/dashboard/manager');
    }

    public function test_invalid_role_id_fails()
    {
        $response = $this->post('/register', [
            'name' => 'User',
            'email' => 'user@test.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role_id' => 999, // Non-existent role
            'terms' => true,
        ]);

        $response->assertSessionHasErrors('role_id');
    }
}
```

### Test Login with Role

```php
public function test_login_with_admin_role_redirects_correctly()
{
    $user = User::factory()->create([
        'email' => 'admin@test.com',
        'role_id' => 1, // Admin role
    ]);

    $response = $this->post('/login', [
        'email' => 'admin@test.com',
        'password' => 'password',
    ]);

    $response->assertRedirect('/dashboard/admin');
    $this->assertAuthenticatedAs($user);
}
```

---

## Common Patterns

### Check Role in Controller

```php
namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return view('dashboard.admin');
        } elseif ($user->hasRole('manager')) {
            return view('dashboard.manager');
        } else {
            return view('dashboard.staff');
        }
    }
}
```

### Get Current User's Permissions (Future Enhancement)

```php
namespace App\Models;

class User extends Authenticatable
{
    // Future: Add permissions relationship
    public function permissions()
    {
        return $this->role->permissions();
    }

    public function can($permission): bool
    {
        return $this->permissions()
            ->where('name', $permission)
            ->exists();
    }
}
```

### Create Custom Blade Directive

```php
// In AppServiceProvider::boot()
Blade::if('admin', function () {
    return auth()->check() && auth()->user()->hasRole('admin');
});

// In Blade template
@admin
    <p>Admin only content</p>
@endadmin
```

---

## Performance Tips

### Eager Load Related Data

```php
// GOOD - Loads users and roles in single query set
$users = User::with('role')->get();

// BAD - N+1 query problem
foreach (User::all() as $user) {
    echo $user->role->name; // Queries role for each user
}
```

### Cache Roles

```php
use Illuminate\Support\Facades\Cache;

$roles = Cache::remember('roles', 3600, function () {
    return Role::all()->keyBy('id');
});
```

### Use Select for Specific Columns

```php
// Only get needed columns
$users = User::select('id', 'name', 'email', 'role_id')
    ->with('role:id,name')
    ->get();
```

---

## Migration & Seeding

### Create Migration with Role

```bash
php artisan make:migration create_roles_table
```

### Seed Sample Data

```php
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $admin = Role::create(['name' => 'admin', 'description' => 'Administrator']);
        $manager = Role::create(['name' => 'manager', 'description' => 'Manager']);
        $staff = Role::create(['name' => 'staff', 'description' => 'Staff Member']);

        // Create sample users
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $admin->id,
        ]);
    }
}
```

---

## Troubleshooting Code

### Debug Role Assignment

```php
// Check if user has role
dd(auth()->user()->role); // Dump and die - shows role object

// Check specific role
dd(auth()->user()->hasRole('admin')); // true/false

// Get role name safely
$roleName = auth()->user()->role?->name ?? 'no_role';

// Check all user attributes
dd(auth()->user()->toArray());
```

### Test Role Redirect

```php
// In web.php or routes
Route::get('/debug/user', function () {
    return response()->json([
        'user' => auth()->user(),
        'role' => auth()->user()->role,
        'role_name' => auth()->user()->role->name,
    ]);
})->middleware('auth');
```
