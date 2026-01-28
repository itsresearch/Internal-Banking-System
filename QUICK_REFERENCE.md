# Quick Reference - Role-Based Authentication System

## Quick Start

### Test the System:

1. **Clear cache (if needed):**

    ```bash
    php artisan cache:clear
    php artisan config:clear
    ```

2. **Register a new user:**
    - Go to registration page
    - Select your desired role (Admin, Manager, or Staff)
    - You'll be automatically redirected to your role's dashboard

3. **Test user credentials:**
    - Email: `test@example.com`
    - Password: `password`
    - Role: Admin (redirects to /dashboard/admin)

---

## Files Modified

### Core Implementation:

- ✅ `app/Models/Role.php` (New)
- ✅ `app/Models/User.php` (Updated)
- ✅ `app/Actions/Fortify/CreateNewUser.php` (Updated)
- ✅ `app/Providers/FortifyServiceProvider.php` (Updated)
- ✅ `app/Http/Middleware/RedirectByRole.php` (New)

### Views:

- ✅ `resources/views/auth/register.blade.php` (Updated - added role selection)
- ✅ `resources/views/dashboard/manager.blade.php` (New)
- ✅ `resources/views/dashboard/staff.blade.php` (New)
- ✅ `resources/views/dashboard/body.blade.php` (Admin - Already existed)

### Routes & Database:

- ✅ `routes/web.php` (Updated)
- ✅ `database/factories/UserFactory.php` (Updated)
- ✅ `database/seeders/DatabaseSeeder.php` (Updated)

---

## Dashboard Routes

| Role    | Route                | View File                                     |
| ------- | -------------------- | --------------------------------------------- |
| Admin   | `/dashboard/admin`   | `resources/views/dashboard/body.blade.php`    |
| Manager | `/dashboard/manager` | `resources/views/dashboard/manager.blade.php` |
| Staff   | `/dashboard/staff`   | `resources/views/dashboard/staff.blade.php`   |

---

## Redirect Logic

### On Registration:

- User selects role during signup
- After successful registration → Automatically logged in → Redirected to role dashboard

### On Login:

- User enters credentials
- After successful authentication → Redirected to role dashboard based on `user.role_id`

### On Homepage (/):

- **Not logged in** → Shows login/register forms
- **Logged in** → Checks role and redirects to appropriate dashboard

---

## Database Data

### Roles (Automatically Created):

```
ID | Name    | Description
1  | admin   | Administrator
2  | manager | Manager
3  | staff   | Staff Member
```

### Test User (Created on first seed):

```
ID | Name      | Email              | Role ID
1  | Test User | test@example.com   | 1 (admin)
```

---

## How to Add More Users with Different Roles

### Via Laravel Tinker:

```php
php artisan tinker

// Create a manager
$user = App\Models\User::create([
    'name' => 'John Manager',
    'email' => 'manager@example.com',
    'password' => Hash::make('password'),
    'role_id' => 2,
    'status' => 'active'
]);

// Create a staff member
$staff = App\Models\User::create([
    'name' => 'Jane Staff',
    'email' => 'staff@example.com',
    'password' => Hash::make('password'),
    'role_id' => 3,
    'status' => 'active'
]);
```

### Via Registration Form:

Simply use the registration page and select the desired role.

---

## Customization

### Change Role Redirects:

Edit `app/Providers/FortifyServiceProvider.php` - Look for `Fortify::redirects('login')`

### Add Role-Specific Features:

- Edit respective dashboard files:
    - Admin: `resources/views/dashboard/body.blade.php`
    - Manager: `resources/views/dashboard/manager.blade.php`
    - Staff: `resources/views/dashboard/staff.blade.php`

### Add More Roles:

1. Create role in database: `Role::create(['name' => 'rolename', 'description' => '...'])`
2. Update registration form dropdowns in `resources/views/auth/register.blade.php`
3. Update validation in `app/Actions/Fortify/CreateNewUser.php` if needed
4. Add redirect logic in `FortifyServiceProvider.php`
5. Create dashboard view for new role

---

## Important Notes

✅ All users require a `role_id` - No role = No redirect
✅ Role-based redirection happens automatically
✅ Unauthenticated users cannot access `/dashboard/*` routes
✅ Each role can have its own dashboard layout and features
✅ System uses Laravel Fortify for authentication
✅ Three predefined roles: Admin, Manager, Staff
