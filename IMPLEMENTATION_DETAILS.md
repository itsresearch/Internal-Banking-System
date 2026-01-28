# Implementation Details & Architecture

## System Overview

The role-based authentication system is built using Laravel Fortify with custom redirection logic. When a user signs up or logs in, they are automatically redirected to a dashboard page specific to their role.

---

## Architecture Flow

```
User Registration
    ↓
Select Role (Admin/Manager/Staff)
    ↓
Validation (role_id must exist in roles table)
    ↓
Create User with role_id
    ↓
Auto-login (Fortify handles this)
    ↓
FortifyServiceProvider redirect logic
    ↓
User redirected to role-specific dashboard
```

---

## Detailed Component Description

### 1. Role Model (`app/Models/Role.php`)

**Purpose:** Represents user roles in the system

**Key Methods:**

- `users()` - HasMany relationship to get all users with this role

**Database Fields:**

- `id` - Primary key
- `name` - Role name (admin, manager, staff)
- `description` - Human-readable description
- `timestamps` - Created/updated at

---

### 2. User Model (`app/Models/User.php`)

**Changes Made:**

- Added `role_id` to fillable array
- Added `role()` BelongsTo relationship
- Added `hasRole($roleName)` helper method

**New Methods:**

```php
public function role()
{
    return $this->belongsTo(Role::class);
}

public function hasRole(string $roleName): bool
{
    return $this->role && $this->role->name === $roleName;
}
```

**Usage:**

```php
$user = Auth::user();
if ($user->hasRole('admin')) {
    // Do something for admins
}
```

---

### 3. Fortify Configuration (`app/Providers/FortifyServiceProvider.php`)

**Key Change:** Added custom redirect handler

```php
Fortify::redirects('login', function () {
    $user = auth()->user();
    $role = $user->role ? $user->role->name : null;

    if ($role === 'admin') {
        return redirect()->route('dashboard.admin');
    } elseif ($role === 'manager') {
        return redirect()->route('dashboard.manager');
    } elseif ($role === 'staff') {
        return redirect()->route('dashboard.staff');
    }

    return redirect('/dashboard');
});
```

**How It Works:**

1. After successful login, this redirect handler is called
2. It fetches the authenticated user's role
3. Based on role name, it returns appropriate redirect route
4. Fortify applies this redirect automatically

---

### 4. CreateNewUser Action (`app/Actions/Fortify/CreateNewUser.php`)

**Purpose:** Handle user registration with role assignment

**Changes:**

- Added `role_id` to validation rules with `exists:roles,id` check
- Pass `role_id` to `User::create()`

**Validation Rules:**

```php
'role_id' => ['required', 'integer', 'exists:roles,id']
```

This ensures:

- Role field is required
- Role must be an integer
- Role ID must actually exist in roles table (referential integrity)

---

### 5. Routes (`routes/web.php`)

**Homepage Route:**

```php
Route::get('/', function () {
    if (auth()->check()) {
        // Redirect based on role
    }
    return view('dashboard.index'); // Login/Register
});
```

**Dashboard Routes (Protected):**

```php
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard/admin', ...)->name('dashboard.admin');
    Route::get('/dashboard/manager', ...)->name('dashboard.manager');
    Route::get('/dashboard/staff', ...)->name('dashboard.staff');
});
```

**Authentication Middleware:**

- `auth:sanctum` - Checks if user is authenticated via Sanctum guard
- `config('jetstream.auth_session')` - Jetstream session validation
- `verified` - Ensures user email is verified (if enabled)

---

### 6. Registration Form (`resources/views/auth/register.blade.php`)

**New Field Added:**

```blade
<div class="mt-4">
    <x-label for="role_id" value="{{ __('Select Your Role') }}" />
    <select id="role_id" name="role_id" class="block mt-1 w-full rounded-md border-gray-300" required>
        <option value="">-- Select a Role --</option>
        <option value="1">Admin</option>
        <option value="2">Manager</option>
        <option value="3">Staff</option>
    </select>
</div>
```

**Features:**

- Uses Jetstream `x-label` component for consistency
- Required field - user must select a role
- Maps role names to their IDs (1, 2, 3)

---

### 7. Dashboard Views

#### Admin Dashboard (`resources/views/dashboard/body.blade.php`)

- Already existed in your project
- Displayed when user logs in as admin

#### Manager Dashboard (`resources/views/dashboard/manager.blade.php`)

- New file created for manager role
- Shows manager-specific information and features

#### Staff Dashboard (`resources/views/dashboard/staff.blade.php`)

- New file created for staff role
- Shows staff-specific information and features

**All dashboards include:**

- Header and sidebar (shared components)
- Welcome message specific to role
- Role-specific information
- Alert boxes and list of role functions
- Footer

---

### 8. Middleware (`app/Http/Middleware/RedirectByRole.php`)

**Purpose:** Optional middleware for route-specific role redirection

**Usage:**

```php
Route::middleware(['auth', 'redirect.by.role'])->group(function () {
    // Routes here will redirect based on role
});
```

**Note:** Currently not applied globally. You can use it if needed for specific routes.

---

## Database Flow

### User Creation Flow:

```
Registration Form
    ↓ (POST /register)
CreateNewUser Action validates input
    ↓
Check role_id exists in roles table
    ↓
Create User record with role_id
    ↓
User logs in automatically (Fortify)
    ↓
Redirect handler gets user.role.name
    ↓
Return appropriate dashboard route
```

### Login Flow:

```
Login Form
    ↓ (POST /login)
Fortify authenticates credentials
    ↓
Session created
    ↓
Redirect handler triggered
    ↓
Fetch user with eager-loaded role
    ↓
Check role name
    ↓
Return role-specific route
    ↓
Browser redirected to role dashboard
```

---

## Key Design Decisions

### 1. **Role ID in SQL Option Values**

```html
<option value="1">Admin</option>
<option value="2">Manager</option>
<option value="3">Staff</option>
```

We use numeric IDs (1, 2, 3) because:

- Matches database role table IDs
- Faster database lookups
- Validation easier with `exists:roles,id`

### 2. **Custom Redirect Handler**

Using `Fortify::redirects()` instead of middleware because:

- Works immediately after login
- Has access to authenticated user
- Cleaner than manually adding to every controller
- Applies to both web and sanctum guards

### 3. **Eager Loading (Optional)**

For better performance with roles:

```php
$user = User::with('role')->find($id);
```

### 4. **Nullable Role Name Check**

```php
$role = $user->role ? $user->role->name : null;
```

Prevents null pointer exceptions if role relationship is broken

---

## Security Considerations

### 1. **Validation**

- `role_id` must exist in roles table
- Prevents users from creating roles that don't exist

### 2. **Authentication Middleware**

- All dashboard routes require authentication
- Users cannot access other roles' dashboards directly

### 3. **Data Integrity**

- Foreign key constraint on `users.role_id`
- Deleting a role cascades to users (handled by migration)

### 4. **Authorization**

For per-user authorization (optional), you can:

```php
if (auth()->user()->hasRole('admin')) {
    // Allow action
}
```

---

## Scalability Notes

### To Add New Roles:

1. Create role in DB: `Role::create(['name' => 'supervisor'])`
2. Update registration form dropdown
3. Add redirect case in `FortifyServiceProvider`
4. Create dashboard view for new role

### For Permissions/Policies:

This system handles roles only. For fine-grained permissions:

1. Create `Permissions` table
2. Create `RolePermission` pivot table
3. Use Laravel's Gate/Policy system
4. Check permissions in controllers

### Database Optimization:

- Add index on `users.role_id`
- Cache roles if not changing frequently:

```php
Cache::remember('roles', 86400, function () {
    return Role::all();
});
```

---

## Testing Scenarios

### Scenario 1: Register as Admin

1. Go to registration
2. Select "Admin"
3. Submit
4. Verify redirected to `/dashboard/admin`

### Scenario 2: Register as Manager

1. Go to registration
2. Select "Manager"
3. Submit
4. Verify redirected to `/dashboard/manager`

### Scenario 3: Register as Staff

1. Go to registration
2. Select "Staff"
3. Submit
4. Verify redirected to `/dashboard/staff`

### Scenario 4: Login with Wrong Password

1. Enter valid email with wrong password
2. Verify rejected with error message
3. Verify NOT logged in

### Scenario 5: Logout and Access

1. User logs out
2. Try accessing `/dashboard/admin`
3. Verify redirected to login page

---

## Troubleshooting

### Issue: "Column not found" errors

**Solution:** Make sure migrations are run: `php artisan migrate`

### Issue: Role dropdown not appearing

**Solution:** Clear Laravel cache: `php artisan cache:clear`

### Issue: Not redirecting to dashboard

**Solution:** Check FortifyServiceProvider `boot()` method is properly registered

### Issue: "Role does not exist" validation error

**Solution:** Ensure roles are seeded: `php artisan db:seed`

---

## Future Enhancements

1. **Email Verification:** Add email verification before accessing dashboards
2. **Two-Factor Authentication:** Extend Fortify's 2FA
3. **Audit Logging:** Track role changes and access
4. **Dynamic Permissions:** Use Spatie Permissions package
5. **Role-Based API:** Add API routes with role checks
6. **Admin Panel:** Create role/user management interface
